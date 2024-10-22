import { Button, Stack } from '@castoredc/matter';
import React from 'react';

interface NodeMappingInterfaceFooterProps {
    dataTransformation: boolean;
    step: 'elements' | 'syntax';
    fieldDescription: React.ReactNode;
    valueDescription: string;
    selectedElements: any[];
    isLoading: boolean;
    setStep: (step: 'elements' | 'syntax') => void;
    handleSubmit: () => void;
}

const NodeMappingInterfaceFooter: React.FC<NodeMappingInterfaceFooterProps> = ({
    dataTransformation,
    step,
    fieldDescription,
    valueDescription,
    selectedElements,
    isLoading,
    setStep,
    handleSubmit,
}) => {
    if (dataTransformation && step === 'elements') {
        return (
            <div className="FormButtons">
                <Stack distribution="equalSpacing">
                    <span>
                        The data will be transformed to <b>{valueDescription}</b>.
                    </span>
                    <Stack distribution="trailing" alignment="end">
                        <span className="FieldCount">
                            {selectedElements.length} field{selectedElements.length !== 1 && 's'} selected
                        </span>

                        <Button onClick={() => setStep('syntax')} disabled={isLoading || selectedElements.length === 0}>
                            Next
                        </Button>
                    </Stack>
                </Stack>
            </div>
        );
    } else if (dataTransformation && step === 'syntax') {
        return (
            <div className="FormButtons">
                <Stack distribution="equalSpacing">
                    <Button buttonType="secondary" onClick={() => setStep('elements')} disabled={isLoading}>
                        Back
                    </Button>

                    <Button onClick={handleSubmit} disabled={isLoading || selectedElements.length === 0}>
                        Save mapping
                    </Button>
                </Stack>
            </div>
        );
    } else {
        return (
            <div className="FormButtons">
                <Stack distribution="equalSpacing">
                    <span>
                        Only {fieldDescription} supporting <b>{valueDescription}</b> can be selected.
                    </span>
                    <Stack distribution="trailing" alignment="end">
                        <span className="FieldCount">
                            {selectedElements.length} field{selectedElements.length !== 1 && 's'} selected
                        </span>

                        <Button onClick={handleSubmit} disabled={isLoading || selectedElements.length === 0}>
                            Save mapping
                        </Button>
                    </Stack>
                </Stack>
            </div>
        );
    }
};

export default NodeMappingInterfaceFooter;
