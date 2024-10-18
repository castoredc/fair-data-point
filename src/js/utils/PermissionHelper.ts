export const isGranted = (attribute, permissions): boolean => {
    return permissions.includes(attribute);
};

export const isAdmin = (user): boolean => {
    return user.isAdmin === true;
};
