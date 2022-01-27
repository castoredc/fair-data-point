import React, {FC, FormEvent, InputHTMLAttributes} from 'react'
import {classNames} from "../../../util";
import {Icon} from "@castoredc/matter";
import {MatterIcon} from "@castoredc/matter-icons";
import CustomIcon from "components/Icon/CustomIcon";
import {FieldProps} from "formik";
import FieldErrors from "components/Input/Formik/Errors";
import _ from "lodash";

interface SelectableListItemsProps extends FieldProps {
    readOnly?: boolean,
    onChange?: (event: FormEvent<HTMLFieldSetElement>) => void,
    options: ListItemProps[],
    serverError?: any,
    multiple?: boolean,
}

interface ListItemProps extends InputHTMLAttributes<HTMLInputElement> {
    title: string;
    description?: string;
    value: string,
    icon?: MatterIcon;
    customIcon?: string;
    fill?: boolean;
    active?: boolean;
    handleChange: () => void;
}

const SelectableListItems: FC<SelectableListItemsProps> = ({
                                     field,
                                     readOnly,
                                     onChange,
                                     options,
                                     serverError,
                                     multiple,
                                 }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return <>
        <div>
            {options.map((option) => {
                return <SelectableListItem
                    key={option.value}
                    title={option.title}
                    description={option.description}
                    icon={option.icon}
                    customIcon={option.customIcon}
                    fill={option.fill}
                    active={multiple ? field.value.includes(option.value) : field.value === option.value}
                    value={option.value}
                    handleChange={() => {
                        const value = multiple ? _.xor(field.value, [option.value]) : option.value;
                        field.onChange({target: {name: field.name, value}})
                    }}
                    role={multiple ? 'checkbox' : 'radio'}
                />
            })}
        </div>

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </>;
}

const SelectableListItem: FC<ListItemProps> = ({
                                         title,
                                         description,
                                         icon,
                                         customIcon,
                                         fill,
                                         active,
                                        handleChange,
    role,
                                     }) => {
    return <div className={classNames("ListItem", "Selectable", active && 'Active')}
                role={role}
              onClick={handleChange}>
        {(icon) && <span className={classNames('ListItemLeftIcon', fill && 'Fill')}>
                <Icon type={icon}/>
            </span>}
        {(customIcon) && <span className={classNames('ListItemLeftIcon', fill && 'Fill')}>
                <CustomIcon type={customIcon}/>
            </span>}
        <span className="ListItemTitle">{title}</span>
        <span className="ListItemDescription">{description}</span>
    </div>;
}

export default SelectableListItems;