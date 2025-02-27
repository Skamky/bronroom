import ApplicationLogo from '@/Components/ApplicationLogo';
import {Link} from '@inertiajs/react';
import {PropsWithChildren} from 'react';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div>
            <div>
                <Link href="/">
                    <ApplicationLogo height={'5rem'} width={'5rem'} className="fill-current text-gray-500"/>
                </Link>
            </div>

            <div>
                {children}
            </div>
        </div>
    );
}
