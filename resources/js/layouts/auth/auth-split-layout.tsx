import { Link, usePage } from '@inertiajs/react';
import { CalendarDays, DoorOpen, Sparkles } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

const highlights = [
    {
        icon: DoorOpen,
        title: 'Browse spaces',
        text: 'Explore rooms with capacity, amenities, and photos at a glance.',
    },
    {
        icon: CalendarDays,
        title: 'Reserve in minutes',
        text: 'Pick a slot that works—overlaps are blocked so schedules stay clean.',
    },
    {
        icon: Sparkles,
        title: 'Built for clarity',
        text: 'A calm interface so you can focus on the meeting, not the software.',
    },
];

export default function AuthSplitLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    const { name } = usePage<{ name: string }>().props;

    return (
        <div className="relative min-h-svh w-full overflow-x-hidden bg-[#f8fafc] lg:grid lg:min-h-dvh lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] xl:grid-cols-[1.12fr_minmax(24rem,32rem)]">
            {/* Mobile page wash — matches Blade shell */}
            <div
                className="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,rgba(15,23,42,0.05),transparent_38%),linear-gradient(to_bottom,#f8fafc,#eef2ff_45%,#f8fafc)] lg:hidden"
                aria-hidden
            />

            {/* Left: welcome / brand (desktop) */}
            <aside className="relative hidden flex-col justify-between overflow-hidden border-r border-white/60 bg-white/40 p-10 backdrop-blur-[2px] lg:flex xl:p-14">
                <div
                    className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_18%_12%,rgba(99,102,241,0.12),transparent_42%),radial-gradient(circle_at_88%_72%,rgba(14,165,233,0.08),transparent_38%),linear-gradient(165deg,rgba(248,250,252,0.95)_0%,rgba(238,242,255,0.85)_48%,rgba(248,250,252,0.98)_100%)]"
                    aria-hidden
                />
                <div
                    className="pointer-events-none absolute -right-24 top-1/4 h-72 w-72 rounded-full bg-indigo-400/15 blur-3xl"
                    aria-hidden
                />
                <div
                    className="pointer-events-none absolute -left-16 bottom-1/4 h-64 w-64 rounded-full bg-sky-400/10 blur-3xl"
                    aria-hidden
                />

                <div className="relative z-10 flex flex-col gap-12">
                    <Link
                        href={home()}
                        className="inline-flex w-fit items-center gap-2.5 rounded-xl outline-none ring-offset-2 ring-offset-[#f8fafc] transition hover:opacity-90 focus-visible:ring-2 focus-visible:ring-gray-900/25"
                    >
                        <AppLogoIcon className="h-9 w-auto sm:h-10" />
                        <span className="font-semibold tracking-tight text-gray-900 text-xl">
                            {name}
                        </span>
                    </Link>

                    <div className="max-w-lg space-y-6">
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-900/55">
                            Welcome
                        </p>
                        <h2 className="text-balance text-3xl font-semibold leading-[1.15] tracking-tight text-gray-900 xl:text-[2rem] xl:leading-tight">
                            The right room, reserved without the hassle.
                        </h2>
                        <p className="text-pretty text-base leading-relaxed text-gray-600">
                            Sign in to manage reservations, or create an account and start booking
                            meeting spaces that fit your team.
                        </p>
                    </div>

                    <ul className="relative z-10 max-w-md space-y-5">
                        {highlights.map(({ icon: Icon, title: line, text }) => (
                            <li key={line} className="flex gap-4">
                                <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-200/80 bg-white/90 text-indigo-700 shadow-sm">
                                    <Icon className="h-5 w-5" strokeWidth={1.75} />
                                </span>
                                <div className="min-w-0 pt-0.5">
                                    <p className="font-medium text-gray-900">{line}</p>
                                    <p className="mt-0.5 text-sm leading-snug text-gray-600">
                                        {text}
                                    </p>
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>
            </aside>

            {/* Right: form card */}
            <div className="flex flex-col justify-center px-4 py-10 sm:px-6 sm:py-12 lg:px-10 lg:py-14 xl:px-12">
                <div className="mx-auto w-full max-w-md">
                    <div className="rounded-2xl border border-white/80 bg-white/95 p-7 shadow-[0_24px_64px_-28px_rgba(15,23,42,0.22)] ring-1 ring-gray-900/[0.04] backdrop-blur-md sm:p-9">
                        <div className="mb-8 flex flex-col gap-6">
                            <Link
                                href={home()}
                                className="flex items-center justify-center gap-2.5 self-center outline-none lg:hidden"
                            >
                                <AppLogoIcon className="h-9 w-auto" />
                                <span className="text-lg font-semibold tracking-tight text-gray-900">
                                    {name}
                                </span>
                            </Link>

                            <div className="space-y-2 text-center lg:text-left">
                                <h1 className="text-2xl font-semibold tracking-tight text-gray-900">
                                    {title}
                                </h1>
                                <p className="text-pretty text-sm leading-relaxed text-gray-600">
                                    {description}
                                </p>
                            </div>
                        </div>

                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
