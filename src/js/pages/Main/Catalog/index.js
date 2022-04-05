import React, { Component } from "react";
import { localizedText } from "../../../util";
import { toast } from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Layout from "../../../components/Layout";
import Header from "../../../components/Layout/Header";
import MainBody from "../../../components/Layout/MainBody";
import { getBreadCrumbs } from "../../../utils/BreadcrumbUtils";
import AssociatedItemsBar from "../../../components/AssociatedItemsBar";
import DatasetList from "../../../components/List/DatasetList";
import StudyList from "../../../components/List/StudyList";
import MetadataSideBar from "../../../components/MetadataSideBar";
import { apiClient } from "src/js/network";

export default class Catalog extends Component {
  constructor(props) {
    super(props);

    this.state = {
      isLoadingFDP: true,
      hasLoadedFDP: false,
      isLoadingCatalog: true,
      hasLoadedCatalog: false,
      fdp: null,
      catalog: null,
      currentItem: null,
    };

    this.datasetsRef = React.createRef();
  }

  componentDidMount() {
    this.getFDP();
    this.getCatalog();
  }

  getFDP = () => {
    apiClient
      .get("/api/fdp")
      .then((response) => {
        this.setState({
          fdp: response.data,
          isLoadingFDP: false,
          hasLoadedFDP: true,
        });
      })
      .catch((error) => {
        this.setState({
          isLoadingFDP: false,
        });

        const message =
          error.response && typeof error.response.data.error !== "undefined"
            ? error.response.data.error
            : "An error occurred while loading the FAIR Data Point information";
        toast.error(<ToastContent type="error" message={message} />);
      });
  };

  getCatalog = () => {
    apiClient
      .get("/api/catalog/" + this.props.match.params.catalog)
      .then((response) => {
        this.setState({
          catalog: response.data,
          currentItem:
            Object.keys(response.data.count).find(
              (key) => response.data.count[key] > 0
            ) ?? null,
          isLoadingCatalog: false,
          hasLoadedCatalog: true,
        });
      })
      .catch((error) => {
        this.setState({
          isLoadingCatalog: false,
        });

        const message =
          error.response && typeof error.response.data.error !== "undefined"
            ? error.response.data.error
            : "An error occurred while loading the catalog information";
        toast.error(<ToastContent type="error" message={message} />);
      });
  };

  handleItemChange = (item) => {
    this.setState({
      currentItem: item,
    });
  };

  render() {
    const { fdp, catalog, isLoadingFDP, isLoadingCatalog, currentItem } =
      this.state;
    const { user, embedded, location } = this.props;

    const breadcrumbs = getBreadCrumbs(location, { fdp, catalog });

    const title = catalog ? localizedText(catalog.metadata.title, "en") : null;

    return (
      <Layout
        className="Catalog"
        title={title}
        isLoading={isLoadingFDP || isLoadingCatalog}
        embedded={embedded}
      >
        <Header
          user={user}
          embedded={embedded}
          breadcrumbs={breadcrumbs}
          title={title}
        />

        <MainBody isLoading={isLoadingFDP || isLoadingCatalog}>
          {catalog && (
            <>
              <div className="MainCol">
                {catalog.metadata.description && !embedded && (
                  <div className="InformationDescription">
                    {localizedText(catalog.metadata.description, "en", true)}
                  </div>
                )}
              </div>
              <div className="SideCol">
                <MetadataSideBar
                  type="catalog"
                  metadata={catalog.metadata}
                  name={title}
                />
              </div>

              <AssociatedItemsBar
                items={catalog.count}
                current={currentItem}
                onClick={this.handleItemChange}
              />

              <StudyList
                visible={currentItem === "study"}
                catalog={catalog}
                state={breadcrumbs.current ? breadcrumbs.current.state : null}
                embedded={embedded}
                showMap
                className="MainCol"
              />

              <DatasetList
                visible={currentItem === "dataset"}
                catalog={catalog}
                state={breadcrumbs.current ? breadcrumbs.current.state : null}
                embedded={embedded}
                className="MainCol"
              />
            </>
          )}
        </MainBody>
      </Layout>
    );
  }
}
