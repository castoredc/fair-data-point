import React, { Component } from "react";
import { toast } from "react-toastify";
import ToastContent from "../../../../components/ToastContent";
import { Button, LoadingOverlay, Stack } from "@castoredc/matter";
import ListItem from "components/ListItem";
import DataGridHelper from "components/DataTable/DataGridHelper";
import * as H from "history";
import { localizedText } from "../../../../util";
import { isGranted } from "utils/PermissionHelper";
import PageBody from "components/Layout/Dashboard/PageBody";
import { apiClient } from "src/js/network";

interface DatasetsProps {
  catalog: string;
  history: H.History;
}

interface DatasetsState {
  datasets: any;
  isLoading: boolean;
  pagination: any;
}

export default class Datasets extends Component<DatasetsProps, DatasetsState> {
  constructor(props) {
    super(props);

    this.state = {
      isLoading: true,
      datasets: [],
      pagination: DataGridHelper.getDefaultState(25),
    };
  }

  componentDidMount() {
    this.getDatasets();
  }

  getDatasets = () => {
    const { catalog } = this.props;
    this.setState({
      isLoading: true,
    });

    apiClient
      .get("/api/catalog/" + catalog + "/dataset")
      .then((response) => {
        this.setState({
          datasets: response.data.results,
          pagination: DataGridHelper.parseResults(response.data),
          isLoading: false,
        });
      })
      .catch((error) => {
        this.setState({
          isLoading: false,
        });

        const message =
          error.response && typeof error.response.data.error !== "undefined"
            ? error.response.data.error
            : "An error occurred while loading the datasets";
        toast.error(<ToastContent type="error" message={message} />);
      });
  };

  render() {
    const { isLoading, datasets } = this.state;
    const { catalog, history } = this.props;

    return (
      <PageBody>
        {isLoading && <LoadingOverlay accessibleLabel="Loading studies" />}

        <Stack distribution="trailing" alignment="end">
          <Button
            icon="add"
            buttonType="primary"
            disabled={isLoading}
            onClick={() =>
              history.push(`/dashboard/catalogs/${catalog}/datasets/add`)
            }
          >
            Add dataset
          </Button>
        </Stack>

        <div>
          {datasets.length === 0 && (
            <div className="NoResults">This study does not have datasets.</div>
          )}

          {datasets.map((dataset) => {
            return (
              <ListItem
                key={dataset.id}
                selectable={false}
                disabled={!isGranted("edit", dataset.permissions)}
                link={`/dashboard/catalogs/${catalog}/datasets/${dataset.slug}`}
                title={
                  dataset.hasMetadata
                    ? localizedText(dataset.metadata.title, "en")
                    : "Untitled dataset"
                }
              />
            );
          })}
        </div>
      </PageBody>
    );
  }
}
