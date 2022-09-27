export type PermissionType = {
    user: PermissionUserType;
    type: string;
};

type PermissionUserType = {
    id: string;
    name: string;
};

type PermissionOptionType = {
    labelText: string,
    value: string,
}