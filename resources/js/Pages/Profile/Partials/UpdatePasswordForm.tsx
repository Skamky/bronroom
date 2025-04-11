import { Button, Text, TextInput } from '@gravity-ui/uikit';
import { useForm } from '@inertiajs/react';
import { FormEventHandler, useRef } from 'react';

export default function UpdatePasswordForm({
    className = '',
}: {
    className?: string;
}) {
    const passwordInput = useRef<HTMLInputElement>(null);
    const currentPasswordInput = useRef<HTMLInputElement>(null);

    const {
        data,
        setData,
        errors,
        put,
        reset,
        processing,
        recentlySuccessful,
    } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const updatePassword: FormEventHandler = (e) => {
        e.preventDefault();

        put(route('password.update'), {
            preserveScroll: true,
            onSuccess: () => reset(),
            onError: (errors) => {
                if (errors.password) {
                    reset('password', 'password_confirmation');
                    passwordInput.current?.focus();
                }

                if (errors.current_password) {
                    reset('current_password');
                    currentPasswordInput.current?.focus();
                }
            },
        });
    };

    return (
        <section className={className}>
            <header className="mb-4 px-3">
                <Text variant="header-2">
                    Update Password
                </Text>

                <Text className="text-muted">
                    Ensure your account is using a long, random password to stay secure.
                </Text>
            </header>

            <form onSubmit={updatePassword} className="mb-4 px-3">
                <div className="mb-3">
                    <Text className="mb-2 d-block">Current Password</Text>

                    <TextInput
                        id="current_password"
                        controlRef={currentPasswordInput}
                        value={data.current_password}
                        onChange={(e) => setData('current_password', e.target.value)}
                        type="password"
                        autoComplete="current-password"
                        error={errors.current_password}
                        className="w-100"
                    />
                </div>

                <div className="mb-3">
                    <Text className="mb-2 d-block">New Password</Text>

                    <TextInput
                        id="password"
                        controlRef={passwordInput}
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        type="password"
                        autoComplete="new-password"
                        error={errors.password}
                        className="w-100"
                    />
                </div>

                <div className="mb-3">
                    <Text className="mb-2 d-block">Confirm Password</Text>

                    <TextInput
                        id="password_confirmation"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        type="password"
                        autoComplete="new-password"
                        error={errors.password_confirmation}
                        className="w-100"
                    />
                </div>

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
