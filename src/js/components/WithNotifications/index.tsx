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
    // Explicitly type the wrapper to include ComponentWithNotifications
    const NotificationsWrapper: React.FC<Omit<P, keyof ComponentWithNotifications>> = (props) => {
        const notifications = useNotifications();

        // Cast the combined props to satisfy TypeScript
        return <WrappedComponent {...(props as P)} notifications={notifications} />;
    };

    return NotificationsWrapper;
}

export interface ComponentWithNotifications {
    notifications: {
        show: (message: SnackbarMessage, options?: OptionsWithExtraProps<VariantType>) => void;
    };
}