import { InertiaLinkProps, Link } from '@inertiajs/react';

export default function NavLink({
    active = false,
    className = '',
    children,
    ...props
}: InertiaLinkProps & { active: boolean }) {
    return (
        <Link
            {...props}
            className={
                'inline-flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200 ' +
                (active
                    ? 'text-primary bg-primary-light/10 '
                    : 'text-secondary hover:text-primary hover:bg-primary-light/5 ') +
                className
            }
        >
            {children}
        </Link>
    );
}
