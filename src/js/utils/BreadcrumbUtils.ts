import { localizedText } from '../util';
import { BreadcrumbsType, BreadcrumbType } from 'types/BreadcrumbType';

export const getBreadCrumbs = (location, data): BreadcrumbsType => {
    const fdp = 'fdp' in data ? data.fdp : location.state && 'fdp' in location.state ? location.state.fdp : null;
    const catalog = 'catalog' in data ? data.catalog : location.state && 'catalog' in location.state ? location.state.catalog : null;
    const study = 'study' in data ? data.study : location.state && 'study' in location.state ? location.state.study : null;
    const dataset = 'dataset' in data ? data.dataset : location.state && 'dataset' in location.state ? location.state.dataset : null;
    const distribution =
        'distribution' in data ? data.distribution : location.state && 'distribution' in location.state ? location.state.distribution : null;
    const query = 'query' in data ? data.query : location.state && 'query' in location.state ? location.state.query : null;

    let crumbs = [
        fdp
            ? {
                  type: 'fdp',
                  title: fdp.hasMetadata ? fdp.metadata.title : 'FAIR Data Point',
                  data: fdp,
                  path: '/fdp',
                  state: { fdp },
              }
            : {
                  type: 'fdp',
                  title: 'FAIR Data Point',
                  data: {
                      title: 'FAIR Data Point',
                  },
                  path: '/fdp',
                  state: {},
              },
        catalog
            ? {
                  type: 'catalog',
                  title: catalog.hasMetadata ? localizedText(catalog.metadata.title) : 'Catalog',
                  data: catalog,
                  path: catalog.relativeUrl,
                  state: { fdp, catalog },
              }
            : null,
        study
            ? {
                  type: 'study',
                  title: study.hasMetadata ? localizedText(study.metadata.title) : 'Study',
                  data: study,
                  path: `/study/${study.slug}`,
                  state: { fdp, catalog, study },
              }
            : null,
        dataset
            ? {
                  type: 'dataset',
                  title: dataset.hasMetadata ? localizedText(dataset.metadata.title) : 'Dataset',
                  data: dataset,
                  path: dataset.relativeUrl,
                  state: { fdp, catalog, study, dataset },
              }
            : null,
        distribution
            ? {
                  type: 'distribution',
                  title: distribution.hasMetadata ? localizedText(distribution.metadata.title) : 'Distribution',
                  data: distribution,
                  path: distribution.relativeUrl,
                  state: { fdp, catalog, study, dataset, distribution },
              }
            : null,
        query
            ? {
                  type: 'query',
                  title: 'Query',
                  path: distribution ? `${distribution.relativeUrl}/query` : '/query',
                  state: { fdp, catalog, study, dataset, query },
              }
            : null,
    ];

    crumbs = crumbs.filter(function (el) {
        return el != null;
    });

    const length = crumbs.length;
    const current = length > 0 ? crumbs[length - 1] : null;
    const previous = length > 1 ? crumbs[length - 2] : null;

    return {
        current,
        previous,
        crumbs: crumbs as BreadcrumbType[],
    };
};
