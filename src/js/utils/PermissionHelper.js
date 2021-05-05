export const isGranted = (attribute, permissions) => {
    return permissions.includes(attribute);
};