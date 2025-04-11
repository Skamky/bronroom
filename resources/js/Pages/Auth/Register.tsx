import {Button, Card, TextInput, Label} from '@gravity-ui/uikit';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Register" />
            <Card className='p-2'>
                <form onSubmit={submit}>
                    <div>
                        <Label>Name</Label>

                        <TextInput
                            size="l"
                            id="name"
                            name="name"
                            value={data.name}
                            className="mt-1 block w-full"
                            autoComplete="name"
                            autoFocus={true}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                        />
                    </div>

                    <div className="mt-4">
                        <Label>Email</Label>

                        <TextInput
                            size="l"
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="mt-1 block w-full"
                            autoComplete="username"
                            onChange={(e) => setData('email', e.target.value)}
                            error={errors.email}
                        />
                    </div>

                    <div className="mt-4">
                        <Label>Password</Label>

                        <TextInput
                            size="l"
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="mt-1 block w-full"
                            autoComplete="new-password"
                            onChange={(e) => setData('password', e.target.value)}
                            error={errors.password}
                        />
                    </div>

                    <div className="mt-4">
                        <Label>Confirm Password</Label>

                        <TextInput
                            size="l"
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            value={data.password_confirmation}
                            className="mt-1 block w-full"
                            autoComplete="new-password"
                            onChange={(e) =>
                                setData('password_confirmation', e.target.value)
                            }
                            error={errors.password_confirmation}
                        />
                    </div>

                    <div className="mt-4 flex items-center justify-end">
                        <Link
                            href={route('login')}
                            className="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                        >
                            Already registered?
                        </Link>

                        <Button
                            className="ms-4"
                            type="submit"
                            view="action"
                            size="l"
                            loading={processing}
                        >
                            Register
                        </Button>
                    </div>
                </form>
            </Card>
        </GuestLayout>
    );
}
