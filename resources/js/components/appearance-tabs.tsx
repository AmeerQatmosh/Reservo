import { Sun } from 'lucide-react';
import type { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

export default function AppearanceTabs({
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement>) {
    return (
        <div
            className={cn(
                'rounded-xl border border-border bg-card px-5 py-4 shadow-sm',
                className,
            )}
            {...props}
        >
            <div className="flex items-start gap-3">
                <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                    <Sun className="size-5" aria-hidden />
                </span>
                <div className="min-w-0 space-y-1">
                    <p className="text-sm font-medium text-foreground">
                        Light theme
                    </p>
                    <p className="text-sm text-muted-foreground">
                        Reservo uses a single light interface so it stays consistent
                        with the rest of the app. Dark mode is not available.
                    </p>
                </div>
            </div>
        </div>
    );
}
