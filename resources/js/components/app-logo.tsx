import { usePage } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    const { name } = usePage<{ name: string }>().props;

    return (
        <>
            <div className="flex h-8 shrink-0 items-center justify-center sm:h-9">
                <AppLogoIcon className="h-5 w-auto sm:h-6" />
            </div>
            <div className="ml-2 hidden min-w-0 flex-1 text-left text-sm leading-tight sm:grid">
                <span className="truncate font-semibold tracking-tight text-sidebar-foreground">
                    {name}
                </span>
                <span className="hidden truncate text-xs font-medium text-sidebar-foreground/60 md:block">
                    Room reservations made simple
                </span>
            </div>
        </>
    );
}
