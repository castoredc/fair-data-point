export const RecruitmentStatus = {
    not_yet_recruiting: 'Not yet recruiting',
    recruiting: 'Recruiting',
    enrolling_by_invitation: 'Enrolling by invitation',
    active_not_recruiting: 'Active, not recruiting',
    suspended: 'Suspended',
    terminated: 'Terminated',
    completed: 'Completed',
    withdrawn: 'Withdrawn',
};

export const StudyType = {
    interventional: 'Interventional',
    observational: 'Observational',
};

export const MethodType = {
    survey: 'Survey',
    registry: 'Registry',
    rct: 'RCT',
    other: 'Other',
};

export const DataType = {
    float: 'Float (number)',
    double: 'Double (number)',
    decimal: 'Decimal (number)',
    integer: 'Integer (number)',
    dateTime: 'Date and time (date/time)',
    date: 'Date (date/time)',
    time: 'Time (date/time)',
    gDay: 'Day (date/time)',
    gMonth: 'Month (date/time)',
    gYear: 'Year (date/time)',
    gYearMonth: 'Year and month (date/time)',
    gMonthDay: 'Month and day (date/time)',
    string: 'String',
    boolean: 'Boolean',
    url: 'URL',
};

export const MetadataFieldType = {
    input: 'Input',
    inputLocale: 'Input (localized)',
    textarea: 'Textarea',
    textareaLocale: 'Input (localized)',
    ontologyConceptBrowser: 'Ontology concept browser',
    datePicker: 'Datepicker',
    timePicker: 'Timepicker',
    // dateAndTimePicker: 'Date and timepicker',
    checkbox: 'Checkbox',
    checkboxes: 'Checkboxes',
    radioButtons: 'Radio buttons',
    dropdown: 'Dropdown',
    languagePicker: 'Language picker',
    licensePicker: 'License picker',
    countryPicker: 'Country picker',
    agentSelector: 'Agent selector',
};

export const ResourceType = {
    catalog: 'Catalog',
    dataset: 'Dataset',
    distribution: 'Distribution',
    study: 'Study',
    fdp: 'FAIR Data Point',
};

export const ValueType = {
    plain: 'Plain',
    annotated: 'Annotated',
};

export const LoginViews = {
    generic: 'page',
    distribution: 'distribution',
    dataset: 'dataset',
};

export const DistributionGenerationStatus = {
    not_updated: 'Not updated',
    success: 'Generated successfully',
    error: 'Not generated due to errors',
    partially: 'Generated, with errors',
};

export const MetadataDisplayType = {
    heading: 'Heading',
    description: 'Description',
    paragraph: 'Paragraph',
    ontologyConcepts: 'Ontology concepts',
    date: 'Date',
    time: 'Time',
    yesNo: 'Yes/No',
    list: 'List',
    language: 'Language',
    license: 'License',
    country: 'Country',
    agents: 'Agents',
};
