import React, {Component} from 'react'
import DataDictionaryGroupModal from "../../../modals/DataDictionaryGroupModal";
import axios from "axios";
import {toast} from "react-toastify";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import ToastContent from "../../../components/ToastContent";
import TripleModal from "../../../modals/TripleModal";
import ConfirmModal from "../../../modals/ConfirmModal";
import SideTabs from "../../../components/SideTabs";
import {Button} from "@castoredc/matter";

export default class DataDictionaryGroups extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:         {
                group:       false,
            },
            groupModalData:   null,
            isLoadingGroups:  true,
            hasLoadedGroups:  false,
            groups:           [],
            currentGroup:     null,
        };
    }

    componentDidMount() {
        this.getGroups();
    }

    getGroups = () => {
        const {dataDictionary, version} = this.props;

        this.setState({
            isLoadingGroups: true,
        });

        axios.get('/api/dictionary/' + dataDictionary.id + '/v/' + version + '/group')
            .then((response) => {
                this.setState({
                    groups:          response.data,
                    isLoadingGroups: false,
                    hasLoadedGroups: true,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoadingGroups: false,
                });
            });
    };

    getOrderOptions = () => {
        const {groups, currentGroup} = this.state;

        let order = [{value: 1, label: 'At the beginning of the data dictionary'}];

        if (groups.length === 0) {
            return order;
        }

        for (let i = 0; i < groups.length; i++) {
            const item = groups[i];

            if (currentGroup === null || (currentGroup && item.id !== currentGroup.id)) {
                const groupNumber = (i + 1);
                order.push({
                    value: (groupNumber + 1),
                    label: 'After Group ' + groupNumber + ' (' + item.title + ')',
                });
            }
        }

        return order;
    };

    openModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
        });
    };

    closeModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    openGroupModal = (groupData) => {
        this.setState({
            currentGroup:   groupData ? groupData : null,
            groupModalData: groupData,
        }, () => {
            this.openModal('group');
        });
    };

    onGroupSaved = () => {
        this.closeModal('group');
        this.getGroups();
    };

    onTripleSaved = () => {
        this.closeModal('triple');
        this.getGroups();
    };

    removeTriple = () => {
        const {dataDictionary, version} = this.props;
        const {currentGroup, tripleModalData} = this.state;

        axios.delete('/api/dictionary/' + dataDictionary.id + '/v/' + version + '/group/' + currentGroup.id + '/triple/' + tripleModalData.id)
            .then(() => {
                this.closeModal('removeTriple');
                this.getGroups();
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred"/>, {
                    position: "top-center",
                });
            });
    };

    render() {
        const {dataDictionary, version} = this.props;
        const {showModal, hasLoadedGroups, hasLoadedNodes, hasLoadedPrefixes, groups, prefixes, currentGroup, groupModalData, tripleModalData} = this.state;

        if (!hasLoadedGroups) {
            return <InlineLoader/>;
        }

        const orderOptions = this.getOrderOptions();

        return <div className="PageBody">
            <DataDictionaryGroupModal
                orderOptions={orderOptions}
                show={showModal.group}
                handleClose={() => {
                    this.closeModal('group')
                }}
                onSaved={this.onGroupSaved}
                dictionaryId={dataDictionary.id}
                versionId={version}
                data={groupModalData}
            />

            {groups.length === 0 ? <div className="NoResults">
                This data dictionary does not have any groups.

                <br /><br />

                <Button icon="add" onClick={() => this.openGroupModal(null)}>Add group</Button>
            </div> : <SideTabs
                hasButtons
                title="Groups"
                actions={
                    <Button icon="add" iconDescription="Add group" onClick={() => this.openGroupModal(null)} />
                }
                tabs={groups.map((element) => {
                    let icons = [];

                    if (element.repeated) {
                        icons.push({
                            icon: 'copy',
                            title: 'This group is repeated'
                        });
                    }

                    if (element.dependent) {
                        icons.push({
                            icon: 'decision',
                            title: 'This group is dependent'
                        });
                    }

                    return {
                        number:  element.order,
                        title:   element.title,
                        icons:   icons,
                        content: "Hoi"
                        // content: <DataDictionaryGroup
                        //              key={element.id}
                        //              id={element.id}
                        //              title={element.title}
                        //              repeated={element.repeated}
                        //              order={element.order}
                        //              groupedTriples={element.groupedTriples}
                        //              dictionaryId={dataDictionary.id}
                        //              versionId={version}
                        //              openGroupModal={() => this.openGroupModal({
                        //                  id:           element.id,
                        //                  title:        element.title,
                        //                  order:        element.order,
                        //                  repeated:     element.repeated,
                        //                  dependent:    element.dependent,
                        //                  dependencies: element.dependencies,
                        //              })}
                        //              openTripleModal={(tripleData) => this.openTripleModal(element, tripleData)}
                        //              openRemoveTripleModal={(tripleData) => this.openRemoveTripleModal(element, tripleData)}
                        //          />,
                    }
                })}
            />
            }
        </div>;
    }
}