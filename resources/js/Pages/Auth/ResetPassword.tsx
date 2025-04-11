import {Button, Card, TextInput, Label} from '@gravity-ui/uikit';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ResetPassword({
    token,
    email,
}: {
    token: string;
    email: string;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('password.store'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Reset Password" />
            <Card className='p-2'>
                <form onSubmit={submit}>
                    <div>
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
                            autoFocus={true}
                            onChange={(e) => setData('password', e.target.value)}
                            error={errors.password}
                        />
                    </div>

                    <div className="mt-4">
                        <Label>Confirm Password</Label>

                        <TextInput
                            size="l"
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
                        <Button
                            className="ms-4"
                            type="submit"
                            view="action"
                            size="l"
                            loading={processing}
                        >
                            Reset Password
                        </Button>
                    </div>
                </form>
            </Card>
        </GuestLayout>
    );
}
