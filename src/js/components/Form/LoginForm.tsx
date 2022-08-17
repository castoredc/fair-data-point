import React, { FC } from 'react';
import { localizedText } from '../../util';
import { Button, CastorNest } from '@castoredc/matter';
import { LoginViews } from 'components/MetadataItem/EnumMappings';
import './LoginForm.scss';
import { CatalogBrandType } from 'types/CatalogType';
import { ServerType } from 'types/ServerType';
import { Field, Form, Formik } from 'formik';
import SelectableListItems from 'components/Input/Formik/SelectableListItems';

interface LoginFormProps {
    catalog?: CatalogBrandType;
    path?: string;
    servers: ServerType[];
    selectedServerId?: string;
    serverLocked?: boolean;
    modal?: boolean;
    brand?: string;
    view?: string;
}

const LoginForm: FC<LoginFormProps> = ({
    catalog,
    path,
    servers,
    selectedServerId,
    serverLocked = false,
    modal = false,
    brand = 'FAIR Data Point',
    view,
}) => {
    const serverIds = servers.map(server => server.id);
    const defaultServer = servers.filter(server => server.default)[0].id;

    return (
        <Formik
            initialValues={{
                server: selectedServerId && serverIds.includes(selectedServerId) ? selectedServerId : defaultServer,
            }}
            onSubmit={values => {
                window.location.href = '/connect/castor/' + values.server + (path ? '?target_path=' + path : '');
            }}
        >
            {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues }) => {
                const viewName = LoginViews[view] || LoginViews['generic'];

                return (
                    <Form>
                        <div className="LoginForm">
                            {catalog ? (
                                <div className="LoginBrand">
                                    {!modal && <h1>{localizedText(catalog.name, 'en')}</h1>}

                                    <div className="LoginText">
                                        <p>
                                            To enter your study in the {localizedText(catalog.name, 'en')} you must be a registered Castor EDC user.
                                        </p>
                                        <p>Please log in with your Castor EDC account and allow the application to access your information.</p>
                                        {!catalog.accessingData && (
                                            <p>
                                                The application only accesses high-level information from your study and will not download nor upload
                                                any data to your study.
                                            </p>
                                        )}
                                    </div>
                                </div>
                            ) : (
                                <div>
                                    {!modal && <h1>{brand}</h1>}

                                    <div className="LoginText">
                                        {!modal && <p>You need to be a registered Castor EDC user in order to access this {viewName}.</p>}
                                        {modal && view !== 'generic' && view !== null && (
                                            <p>You need to be a registered Castor EDC user in order to access this {viewName}.</p>
                                        )}

                                        <p>Please log in with your Castor EDC account and allow the application to access your information.</p>
                                    </div>
                                </div>
                            )}

                            {!serverLocked && (
                                <div className="Servers">
                                    <div className="ServerText">My study is located on a Castor server in</div>
                                    <div className="ServersList">
                                        <Field
                                            component={SelectableListItems}
                                            name="server"
                                            options={servers.map(server => {
                                                return {
                                                    title: server.name,
                                                    value: server.id,
                                                    customIcon: 'flag' + server.flag.toUpperCase(),
                                                };
                                            })}
                                        />
                                    </div>
                                </div>
                            )}

                            <div className="LoginButton">
                                <Button type="submit" disabled={values.server === null}>
                                    <CastorNest className="LoginButtonLogo" />
                                    Log in with Castor
                                </Button>
                            </div>
                        </div>
                    </Form>
                );
            }}
        </Formik>
    );
};

export default LoginForm;
