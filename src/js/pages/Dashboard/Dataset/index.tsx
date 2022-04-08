import React, { Component } from "react";
import { toast } from "react-toastify";
import ToastContent from "components/ToastContent";
import { Button, LoadingOverlay } from "@castoredc/matter";
import { Route, Switch } from "react-router-dom";
import DocumentTitle from "components/DocumentTitle";
import { localizedText } from "../../../util";
import Header from "components/Layout/Dashboard/Header";
import Body from "components/Layout/Dashboard/Body";
import DatasetForm from "components/Form/Admin/DatasetForm";
import DatasetMetadataForm from "components/Form/Metadata/DatasetMetadataForm";
import SideBar from "components/SideBar";
import NotFound from "pages/ErrorPages/NotFound";
import Distributions from "pages/Dashboard/Dataset/Distributions";
import AddDistribution from "pages/Dashboard/Dataset/AddDistribution";
import { AuthorizedRouteComponentProps } from "components/Route";
import { isGranted } from "utils/PermissionHelper";
import PermissionEditor from "components/PermissionEditor";
import NoPermission from "pages/ErrorPages/NoPermission";
import PageBody from "components/Layout/Dashboard/PageBody";
import { apiClient } from "src/js/network";

interface DatasetProps extends AuthorizedRouteComponentProps {
  study?: any;
  catalog?: any;
}

interface DatasetState {
  dataset: any;
  isLoading: boolean;
}

export default class Dataset extends Component<DatasetProps, DatasetState> {
  constructor(props) {
    super(props);

    this.state = {
      dataset: null,
      isLoading: true,
    };
  }

  getDataset = () => {
    this.setState({
      isLoading: true,
    });

    const { match } = this.props;

    apiClient
      .get("/api/dataset/" + match.params.dataset)
      .then((response) => {
        this.setState({
          dataset: response.data,
          isLoading: false,
        });
      })
      .catch((error) => {
        this.setState({
          isLoading: false,
        });

        if (
          error.response &&
          typeof error.response.data.error !== "undefined"
        ) {
          toast.error(
            <ToastContent type="error" message={error.response.data.error} />
          );
        } else {
          toast.error(
            <ToastContent
              type="error"
              message="An error occurred while loading your dataset"
            />
          );
        }
      });
  };

  componentDidMount() {
    this.getDataset();
  }

  render() {
    const { history, location, match, user } = this.props;
    const { isLoading, dataset } = this.state;

    if (!match.params.study && !match.params.catalog) {
      return null;
    }

    if (isLoading) {
      return <LoadingOverlay accessibleLabel="Loading dataset" />;
    }

    if (!isGranted("edit", dataset.permissions)) {
      return (
        <NoPermission text="You do not have permission to edit this dataset" />
      );
    }

    const title = dataset.hasMetadata
      ? localizedText(dataset.metadata.title, "en")
      : "Untitled dataset";

    const mainUrl = match.params.study
      ? "/dashboard/studies/" + match.params.study
      : "/dashboard/catalogs/" + match.params.catalog;

    return (
      <>
        <DocumentTitle title={title} />

        <SideBar
          back={{
            to: mainUrl,
            title: match.params.study ? "Back to study" : "Back to catalog",
          }}
          location={location}
          items={[
            {
              to: mainUrl + "/datasets/" + dataset.slug,
              exact: true,
              title: "Dataset",
              customIcon: "dataset",
            },
            {
              to: mainUrl + "/datasets/" + dataset.slug + "/metadata",
              exact: true,
              title: "Metadata",
              customIcon: "metadata",
            },
            ...(isGranted("manage", dataset.permissions)
              ? [
                  {
                    to: mainUrl + "/datasets/" + dataset.slug + "/permissions",
                    exact: true,
                    title: "Permissions",
                    icon: "usersLight",
                  },
                ]
              : []),
            {
              type: "separator",
            },
            {
              to: mainUrl + "/datasets/" + dataset.slug + "/distributions",
              exact: true,
              title: "Distributions",
              customIcon: "distribution",
            },
          ]}
        />

        <Body>
          <Header title={title}>
            <Button
              buttonType="contentOnly"
              icon="openNewWindow"
              href={`/fdp/dataset/${dataset.slug}`}
              target="_blank"
            >
              View
            </Button>
          </Header>

          <Switch>
            <Route
              path={[
                "/dashboard/studies/:study/datasets/:dataset",
                "/dashboard/catalogs/:catalog/datasets/:dataset",
              ]}
              exact
              render={(props) => (
                <PageBody>
                  <DatasetForm dataset={dataset} />
                </PageBody>
              )}
            />
            <Route
              path={[
                "/dashboard/studies/:study/datasets/:dataset/metadata",
                "/dashboard/catalogs/:catalog/datasets/:dataset/metadata",
              ]}
              exact
              render={(props) => (
                <PageBody>
                  <DatasetMetadataForm
                    dataset={dataset}
                    onSave={this.getDataset}
                  />
                </PageBody>
              )}
            />
            <Route
              path={[
                "/dashboard/studies/:study/datasets/:dataset/permissions",
                "/dashboard/catalogs/:catalog/datasets/:dataset/permissions",
              ]}
              exact
              render={(props) =>
                isGranted("manage", dataset.permissions) ? (
                  <PermissionEditor
                    getObject={this.getDataset}
                    type="dataset"
                    object={dataset}
                    user={user}
                    {...props}
                  />
                ) : (
                  <NoPermission text="You do not have access to this page" />
                )
              }
            />
            <Route
              path={[
                "/dashboard/studies/:study/datasets/:dataset/distributions",
                "/dashboard/catalogs/:catalog/datasets/:dataset/distributions",
              ]}
              exact
              render={(props) => <Distributions {...props} user={user} />}
            />
            <Route
              path={[
                "/dashboard/studies/:study/datasets/:dataset/distributions/add",
                "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/add",
              ]}
              exact
              render={(props) => <AddDistribution {...props} user={user} />}
            />

            <Route component={NotFound} />
          </Switch>
        </Body>
      </>
    );
  }
}
