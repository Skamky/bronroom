import { Button, Text, TextInput } from '@gravity-ui/uikit';
import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function UpdateProfileInformation({
    mustVerifyEmail,
    status,
    className = '',
}: {
    mustVerifyEmail: boolean;
    status?: string;
    className?: string;
}) {
    const user = usePage().props.auth.user;

    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm({
            name: user.name,
            email: user.email,
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route('profile.update'));
    };

    return (
        <section className={className}>
            <header className="mb-4 px-3">
                <Text variant="header-2">
                    Profile Information
                </Text>

                <Text className="text-muted">
                    Update your account's profile information and email address.
                </Text>
            </header>

            <form onSubmit={submit} className="mb-4 px-3">
                <div className="mb-3">
                    <Text className="mb-2 d-block">Name</Text>

                    <TextInput
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        hasClear
                        autoFocus
                        autoComplete="name"
                        error={errors.name}
                        className="w-100"
                    />
                </div>

                <div className="mb-3">
                    <Text className="mb-2 d-block">Email</Text>

                    <TextInput
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        hasClear
                        autoComplete="username"
                        error={errors.email}
                        className="w-100"
                    />
                </div>

                {mustVerifyEmail && user.email_verified_at === null && (
                    <div className="mb-3">
                        <Text className="text-muted">
                            Your email address is unverified.
                            <Link
                                href={route('verification.send')}
                                method="post"
                                as="button"
                                className="btn btn-link p-0"
                            >
                                Click here to re-send the verification email.
                            </Link>
                        </Text>

                        {status === 'verification-link-sent' && (
                            <Text color="positive">
                                A new verification link has been sent to your email address.
                            </Text>
                        )}
                    </div>
                )}

                <div className="d-flex align-items-center gap-3">
                    <Button
                        view="action"
                        size="l"
                        type="submit"
                        loading={processing}
                    >
                        Save
                    </Button>

                    {recentlySuccessful && (
                        <Text color="positive">
                            Saved.
                        </Text>
                    )}
                </div>
            </form>
        </section>
    );
}
