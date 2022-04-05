import React, { Component } from "react";
import { toast } from "react-toastify";
import ToastContent from "../ToastContent";
import {
  CellText,
  DataGrid,
  Icon,
  IconCell,
  LoadingOverlay,
} from "@castoredc/matter";
import { localizedText } from "../../util";
import DataGridContainer from "./DataGridContainer";
import DataGridHelper from "./DataGridHelper";
import { apiClient } from "src/js/network";

export default class DistributionsDataTable extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isLoadingDistributions: true,
      hasLoadedDistributions: false,
      distributions: [],
      pagination: DataGridHelper.getDefaultState(25),
    };

    this.tableRef = React.createRef();
  }

  componentDidMount() {
    this.getDistributions();
  }

  getDistributions = () => {
    const { pagination, hasLoadedDistributions } = this.state;
    const { dataset } = this.props;

    this.setState({
      isLoadingDistributions: true,
    });

    const filters = {
      page: pagination.currentPage,
      perPage: pagination.perPage,
    };

    if (hasLoadedDistributions) {
      window.scrollTo(0, this.tableRef.current.offsetTop - 35);
    }

    apiClient
      .get(
        dataset
          ? "/api/dataset/" + dataset.slug + "/distribution"
          : "/api/distribution",
        { params: filters }
      )
      .then((response) => {
        this.setState({
          distributions: response.data.results,
          pagination: DataGridHelper.parseResults(response.data),
          isLoadingDistributions: false,
          hasLoadedDistributions: true,
        });
      })
      .catch((error) => {
        this.setState({
          isLoadingDistributions: false,
        });

        const message =
          error.response && typeof error.response.data.error !== "undefined"
            ? error.response.data.error
            : "An error occurred while loading the distributions";
        toast.error(<ToastContent type="error" message={message} />);
      });
  };

  handlePagination = (paginationCount) => {
    const { pagination } = this.state;

    this.setState(
      {
        pagination: {
          ...pagination,
          currentPage: paginationCount.currentPage + 1,
          perPage: paginationCount.pageLimit,
        },
      },
      () => {
        this.getDistributions();
      }
    );
  };

  handleClick = (rowId) => {
    const { distributions } = this.state;
    const { history, dataset } = this.props;
    const distribution = distributions[rowId];

    history.push(
      "/admin" +
        (dataset ? "/dataset/" + dataset.slug : "") +
        "/distribution/" +
        distribution.slug
    );
  };

  render() {
    const {
      distributions,
      isLoadingDistributions,
      hasLoadedDistributions,
      pagination,
    } = this.state;

    if (!hasLoadedDistributions) {
      return <LoadingOverlay accessibleLabel="Loading distributions" />;
    }

    const columns = [
      {
        Header: "Title",
        accessor: "title",
      },
      {
        Header: "Description",
        accessor: "description",
      },
      {
        Header: "Type",
        accessor: "type",
      },
      {
        Header: "Language",
        accessor: "language",
      },
      {
        Header: "License",
        accessor: "license",
      },
      {
        Header: <Icon description="Published" type="view" />,
        accessor: "published",
        disableResizing: true,
        width: 32,
      },
    ];

    const rows = distributions.map((item) => {
      return {
        title: (
          <CellText>
            {item.hasMetadata
              ? localizedText(item.metadata.title, "en")
              : "(no title)"}
          </CellText>
        ),
        description: (
          <CellText>
            {item.hasMetadata
              ? localizedText(item.metadata.description, "en")
              : ""}
          </CellText>
        ),
        type: <CellText>{item.type ? item.type.toUpperCase() : ""}</CellText>,
        language: (
          <CellText>{item.hasMetadata ? item.metadata.language : ""}</CellText>
        ),
        license: (
          <CellText>{item.hasMetadata ? item.metadata.license : ""}</CellText>
        ),
        published: item.published ? (
          <IconCell icon={{ type: "view" }} />
        ) : undefined,
      };
    });

    return (
      <DataGridContainer
        pagination={pagination}
        handlePageChange={this.handlePagination}
        fullHeight
        isLoading={isLoadingDistributions}
        forwardRef={this.tableRef}
      >
        <DataGrid
          accessibleName="Distributions"
          emptyStateContent="No distributions found"
          onClick={this.handleClick}
          rows={rows}
          columns={columns}
        />
      </DataGridContainer>
    );
  }
}
