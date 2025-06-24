import React from 'react';
import { OptionsWithExtraProps, SnackbarMessage, useSnackbar, VariantType } from 'notistack';

export const useNotifications = () => {
    const { enqueueSnackbar } = useSnackbar();

    return {
        show: (message: SnackbarMessage, options?: OptionsWithExtraProps<VariantType>) => {
            enqueueSnackbar(message, options);
        },
    };
};

export default function withNotifications<P extends object>(WrappedComponent: React.ComponentType<P & ComponentWithNotifications>) {
    return function NotificationsWrapper(props: P) {
        const notifications = useNotifications();

        return <WrappedComponent {...props} notifications={notifications} />;
    };
}

export interface ComponentWithNotifications {
    notifications: {
        show: (message: SnackbarMessage, options?: OptionsWithExtraProps<VariantType>) => void;
    };
}