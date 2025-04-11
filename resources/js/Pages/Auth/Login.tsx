import {Button, Card, Checkbox, TextInput, Label, Text} from '@gravity-ui/uikit';
import GuestLayout from '@/Layouts/GuestLayout';
import {Head, Link, useForm} from '@inertiajs/react';
import {FormEventHandler} from 'react';

export default function Login({
                                  status,
                                  canResetPassword,
                              }: {
    status?: string;
    canResetPassword: boolean;
}) {
    const {data, setData, post, processing, errors, reset} = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in"/>

            {status && (
                <div className="">
                    {status}
                </div>
            )}
            <Card className='p-2'>
                <form onSubmit={submit}>
                    <div>
                        <Label>Email</Label>

                        <TextInput
                            autoFocus={true}
                            size={'l'}
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
                            size={'l'}
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="mt-1 block w-full"
                            autoComplete="current-password"
                            onChange={(e) => setData('password', e.target.value)}
                            error={errors.password}
                        />
                    </div>

                    <div className="mt-4 block">
                        <label className="flex items-center">
                            <Checkbox size={'l'}
                                      name="remember"
                                      checked={data.remember}
                                      onChange={(e) =>
                                          setData(
                                              'remember',
                                              (e.target.checked || false) as false,
                                          )
                                      }
                            />
                            <span className="ms-2 text-sm text-gray-600 dark:text-gray-400">
                            Remember me
                        </span>
                        </label>
                    </div>

                    <div className="mt-4 flex items-center justify-end">
                        {canResetPassword && (
                            <Link
                                href={route('password.request')}
                            >
                                Forgot your password?
                            </Link>
                        )}

                        <Button
                            className="ms-4"
                            type="submit"
                            view="action"
                            size="l"
                            loading={processing}
                        >
                            Log in
                        </Button>
                    </div>
                </form>
            </Card>
        </GuestLayout>
    );
}
