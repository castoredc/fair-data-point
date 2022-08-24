export const isGranted = (attribute, permissions) => {
    return permissions.includes(attribute);
};

export const isAdmin = user => {
    return user.isAdmin === true;
};
