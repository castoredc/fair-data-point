import ScrollShadow from "../ScrollShadow";
import {classNames} from "../../util";
import React from "react";

export const StudyStructureNavigator = ({contents, selectedStep, handleStepSwitch}) => {
    return <div className="StudyStructureNavigator">
        <ScrollShadow>
            <div className="StructureElements">
                {contents.map((structureElement) => {
                    return <div key={structureElement.id} className="StructureElement">
                        <div className="StructureElementName">
                            {structureElement.name}
                        </div>
                        <div className="Steps">
                            {structureElement.steps.map((step) => {
                                const active = selectedStep !== null && selectedStep.id === step.id;
                                return <button key={step.id}
                                               className={classNames('Step', active && 'active')}
                                               type="button"
                                               onClick={() => {
                                                   handleStepSwitch(step)
                                               }}
                                >
                                    {step.position}. {step.name}
                                </button>;
                            })}
                        </div>
                    </div>;
                })}
            </div>
        </ScrollShadow>
    </div>
};

export default StudyStructureNavigator;