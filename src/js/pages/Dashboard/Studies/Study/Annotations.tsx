import React, {Component} from "react";
import {Dropdown, FormLabel, LoadingOverlay, Separator} from "@castoredc/matter";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import OptionGroup from "components/StudyStructure/OptionGroup";

interface AnnotationsProps {
    studyId: string,
}

interface AnnotationsState {
    isLoading: boolean,
    showModal: boolean,
    optionGroups: any,
    selectedOptionGroup: string,
}

export default class Annotations extends Component<AnnotationsProps, AnnotationsState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal:    false,
            isLoading: false,
            optionGroups: [],
            selectedOptionGroup: '',
        };
    }

    componentDidMount() {
        this.getOptionGroups();
    }

    getOptionGroups = () => {
        const { studyId } = this.props;
        const { selectedOptionGroup } = this.state;
        this.setState({
            isLoading: true,
        });

        axios.get('/api/castor/study/' + studyId + '/optiongroups')
            .then((response) => {
                this.setState({
                    optionGroups: response.data,
                    selectedOptionGroup: selectedOptionGroup !== '' ? selectedOptionGroup : (response.data.length > 0 ? response.data[0].id : ''),
                    isLoading: false,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    updateSelection = (option) => {
        this.setState({
            selectedOptionGroup: option.value,
        });
    }

    render() {
        const {studyId} = this.props;
        const {isLoading, optionGroups, selectedOptionGroup} = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading option groups" />;
        }

        const options = optionGroups.map((optionGroup) => {
            return {value: optionGroup.id, label: optionGroup.name};
        });

        const optionGroup = optionGroups.find((optionGroup) => {
            return optionGroup.id === selectedOptionGroup;
        });

        if(optionGroups && optionGroups.length === 0) {
            return <div className="NoResults">This study does not have option groups.</div>
        }

        return <div className="PageBody">
            <FormLabel>Option group</FormLabel>
            <Dropdown
                options={options}
                menuPlacement={"auto"}
                menuPosition="fixed"
                getOptionLabel={({label}) => label }
                getOptionValue={({value}) => value }
                onChange={this.updateSelection}
                value={selectedOptionGroup ? {value: optionGroup.id, label: optionGroup.name} : undefined}
            />

            <Separator spacing="comfortable" />

            {optionGroup && <OptionGroup studyId={studyId} onUpdate={this.getOptionGroups} {...optionGroup} />}
        </div>;
    }
}