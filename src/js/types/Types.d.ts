export type ValueLabel = {
    value: string;
    label: string;
};

export type Types = {
    fieldTypes: {
        plain: {
            [key: string]: ValueLabel[];
        };
        annotated: ValueLabel[];
    };
    dataTypes: ValueLabel[];
    displayTypes: {
        plain: {
            [displayType: string]: ValueLabel[];
        };
        annotated: ValueLabel[];
    };
};
