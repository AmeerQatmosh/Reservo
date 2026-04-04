import { AlertTriangle } from 'lucide-react';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { cn } from '@/lib/utils';

const panelClass =
    'border-amber-200/90 bg-amber-50/95 text-amber-950 shadow-sm [&>svg]:text-amber-700';

type Props = {
    variant?: 'register' | 'login';
    className?: string;
};

export function AuthPrivacyNotice({ variant = 'register', className }: Props) {
    if (variant === 'login') {
        return (
            <Alert
                role="note"
                className={cn(panelClass, 'rounded-xl px-3.5 py-3', className)}
            >
                <AlertTriangle className="size-4 shrink-0" aria-hidden />
                <AlertTitle className="text-sm font-semibold text-amber-950">
                    Demo / portfolio site
                </AlertTitle>
                <AlertDescription className="text-sm leading-snug text-amber-900/90">
                    Do not use your real email, name, or a password you use anywhere
                    else. This app is not run as a production service and we do not
                    guarantee how your data is stored or who may see it.
                </AlertDescription>
            </Alert>
        );
    }

    return (
        <Alert
            role="note"
            className={cn(panelClass, 'rounded-xl px-3.5 py-3.5', className)}
        >
            <AlertTriangle className="size-4 shrink-0" aria-hidden />
            <AlertTitle className="text-sm font-semibold text-amber-950">
                Before you create an account
            </AlertTitle>
            <AlertDescription className="text-sm leading-relaxed text-amber-900/90">
                <p>
                    Reservo here is a <strong className="font-semibold">demonstration</strong>{' '}
                    project, not a commercial product. Please do{' '}
                    <strong className="font-semibold">not</strong> enter real personal
                    information, a real email address, or any password you reuse on other
                    sites.
                </p>
                <p className="mt-2">
                    We do not promise production-grade security or privacy; treat anything
                    you submit as non-confidential.
                </p>
            </AlertDescription>
        </Alert>
    );
}
