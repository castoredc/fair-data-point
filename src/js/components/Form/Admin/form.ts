import * as Yup from 'yup';

export const EDCServerDefaultData = {
    id: null,
    name: '',
    url: 'https://',
    flag: 'nl',
    default: false,
    clientId: '',
    clientSecret: '',
};

export const EDCServerSchema = Yup.object().shape({
    name: Yup.string().trim().required('Please enter a name'),
    url: Yup.string().trim().required('Please enter the server URL'),
    flag: Yup.string().trim().required('Please enter the two-letter flag identifier'),
    default: Yup.boolean().required('Please enter if this is the new default Server'),
    clientId: Yup.string().trim().required('Please enter the client ID'),
    clientSecret: Yup.string().trim().required('Please enter the client secret'),
});
