import type { ImgHTMLAttributes } from 'react';

import { cn } from '@/lib/utils';

/**
 * Full-color mark (#101828) — use on light backgrounds only.
 */
export default function AppLogoIcon({
    className,
    alt = '',
    ...props
}: ImgHTMLAttributes<HTMLImageElement>) {
    return (
        <img
            src="/images/reservo-logo-colored.svg"
            alt={alt}
            width={189}
            height={294}
            draggable={false}
            className={cn(
                'h-8 w-auto max-h-full object-contain object-center',
                className,
            )}
            {...props}
        />
    );
}
