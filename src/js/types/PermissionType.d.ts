export type PermissionType = {
    user: PermissionUserType,
    type: string,
};

type PermissionUserType = {
    id: string,
    name: string,
}