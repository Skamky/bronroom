import {Button, Card, TextInput, Text} from '@gravity-ui/uikit';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ForgotPassword({ status }: { status?: string }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('password.email'));
    };

    return (
        <GuestLayout>
            <Head title="Forgot Password" />
            <Card className='p-2'>
                <Text className="mb-4">
                    Forgot your password? No problem. Just let us know your email
                    address and we will email you a password reset link that will
                    allow you to choose a new one.
                </Text>

                <br />

                {status && (
                    <Text color="positive" className="mb-4">
                        {status}
                    </Text>
                )}

                <form onSubmit={submit}>
                    <TextInput
                        size="l"
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoFocus={true}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                    />

                    <div className="mt-4 flex items-center justify-end">
                        <Button
                            className="ms-4"
                            type="submit"
                            view="action"
                            size="l"
                            loading={processing}
                        >
                            Email Password Reset Link
                        </Button>
                    </div>
                </form>
            </Card>
        </GuestLayout>
    );
}
