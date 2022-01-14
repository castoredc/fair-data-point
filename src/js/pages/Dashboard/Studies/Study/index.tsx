import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../../components/ToastContent";
import {Banner, Button, LoadingOverlay} from "@castoredc/matter";
import {Route, RouteComponentProps, Switch} from 'react-router-dom';
import DocumentTitle from "components/DocumentTitle";
import {localizedText} from "../../../../util";
import StudyDetailsForm from "components/Form/Study/StudyDetailsForm";
import ContactsForm from "components/Form/Study/ContactsForm";
import OrganizationsForm from "components/Form/Study/OrganizationsForm";
import Header from "components/Layout/Dashboard/Header";
import Body from "components/Layout/Dashboard/Body";
import Annotations from "pages/Dashboard/Studies/Study/Annotations";
import {isGranted} from "utils/PermissionHelper";
import Datasets from "pages/Dashboard/Studies/Study/Datasets";
import SideBar from "components/SideBar";
import NotFound from "pages/NotFound";

interface StudyProps extends RouteComponentProps<any> {
}

interface StudyState {
    study: any,
    isLoading: boolean,
}

export default class Study extends Component<StudyProps, StudyState> {
    constructor(props) {
        super(props);

        this.state = {
            study: null,
            isLoading: true,
        };
    }

    getStudy = () => {
        this.setState({
            isLoading: true,
        });

        const {match} = this.props;

        axios.get('/api/study/' + match.params.study)
            .then((response) => {
                this.setState({
                    study: response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred while loading the study"/>);
                }
            });
    };

    componentDidMount() {
        this.getStudy();
    }

    render() {
        const {history, location, match} = this.props;
        const {isLoading, study} = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading study"/>;
        }

        const title = study.hasMetadata ? localizedText(study.metadata.briefName, 'en') : study.name;

        return <>
            <DocumentTitle title={title}/>

            <SideBar
                back={{
                    to: '/dashboard/studies',
                    title: 'Back to study list'
                }}
                location={location}
                items={[
                    {
                        to: '/dashboard/studies/' + study.id,
                        exact: true,
                        title: 'Study information',
                        icon: 'study'
                    },
                    {
                        to: '/dashboard/studies/' + study.id + '/team',
                        exact: true,
                        title: 'Study team',
                        customIcon: 'contacts'
                    },
                    {
                        to: '/dashboard/studies/' + study.id + '/centers',
                        exact: true,
                        title: 'Participating centers',
                        customIcon: 'center'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: '/dashboard/studies/' + study.id + '/annotations',
                        exact: true,
                        title: 'Annotations',
                        customIcon: 'annotations'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: '/dashboard/studies/' + study.id + '/datasets',
                        exact: true,
                        title: 'Datasets',
                        customIcon: 'dataset'
                    },
                ]}
            />

            <Body>
                <Header title={title}>
                    <Button
                        buttonType="contentOnly"
                        icon="openNewWindow"
                        href={`/study/${study.slug}`}
                        target="_blank"
                    >
                        View
                    </Button>
                </Header>

                <Switch>
                    <Route path="/dashboard/studies/:study" exact
                           render={(props) => <div>
                               <StudyDetailsForm
                                   studyId={study.id}
                               />
                           </div>
                           }/>
                    <Route path="/dashboard/studies/:study/team" exact
                           render={(props) => <div>
                               <ContactsForm studyId={study.id}/>
                           </div>
                           }/>
                    <Route path="/dashboard/studies/:study/centers" exact
                           render={(props) => <div>
                               <OrganizationsForm studyId={study.id}/>
                           </div>
                           }/>
                    <Route path="/dashboard/studies/:study/annotations" exact
                           render={(props) => isGranted('edit_source_system', study.permissions) ? <div>
                               <Annotations studyId={study.id}/>
                           </div> : <div>
                               <Banner type="error" title="You do not have permission to access this study in Castor"/>
                           </div>
                           }/>
                    <Route path="/dashboard/studies/:study/datasets" exact
                           render={(props) => <div>
                               <Datasets studyId={study.id} history={history}/>
                           </div>
                           }/>
                    <Route component={NotFound}/>
                </Switch>
            </Body>
        </>;
    }
}
