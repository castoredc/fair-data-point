export type BreadcrumbType = {
    type: string;
    path: string;
    state?: any;
    title: string;
};

export type BreadcrumbsType = {
    current: BreadcrumbType | null;
    previous: BreadcrumbType | null;
    crumbs: BreadcrumbType[];
};
