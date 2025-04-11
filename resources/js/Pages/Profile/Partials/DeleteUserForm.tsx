import { Button, Modal, Text, TextInput } from '@gravity-ui/uikit';
import { useForm } from '@inertiajs/react';
import { FormEventHandler, useRef, useState } from 'react';

export default function DeleteUserForm({
    className = '',
}: {
    className?: string;
}) {
    const [confirmingUserDeletion, setConfirmingUserDeletion] = useState(false);
    const passwordInput = useRef<HTMLInputElement>(null);

    const {
        data,
        setData,
        delete: destroy,
        processing,
        reset,
        errors,
        clearErrors,
    } = useForm({
        password: '',
    });

    const confirmUserDeletion = () => {
        setConfirmingUserDeletion(true);
    };

    const deleteUser: FormEventHandler = (e) => {
        e.preventDefault();

        destroy(route('profile.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput.current?.focus(),
            onFinish: () => reset(),
        });
    };

    const closeModal = () => {
        setConfirmingUserDeletion(false);
        clearErrors();
        reset();
    };

    return (
        <section className={className}>
            <header className="mb-4 px-3">
                <Text variant="header-2">
                    Delete Account
                </Text>

                <Text className="text-muted">
                    Once your account is deleted, all of its resources and data will be permanently deleted. 
                    Before deleting your account, please download any data or information that you wish to retain.
                </Text>
            </header>

            <Button
                view="outlined-danger"
                size="l"
                onClick={confirmUserDeletion}
                className="px-3"
            >
                Delete Account
            </Button>

            <Modal
                open={confirmingUserDeletion}
                onClose={closeModal}
            >
                <div className="p-4">
                    <Text variant="header-2" className="mb-4">
                        Delete Account
                    </Text>

                    <form onSubmit={deleteUser}>
                        <Text className="mb-4">
                            Are you sure you want to delete your account? Once your account is deleted, 
                            all of its resources and data will be permanently deleted. 
                            Please enter your password to confirm you would like to permanently delete your account.
                        </Text>

                        <div className="mb-4">
                            <TextInput
                                id="password"
                                type="password"
                                name="password"
                                controlRef={passwordInput}
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder="Password"
                                error={errors.password}
                                className="w-100"
                            />
                        </div>

                        <div className="d-flex justify-content-end gap-3">
                            <Button
                                view="outlined"
                                size="l"
                                onClick={closeModal}
                            >
                                Cancel
                            </Button>

                            <Button
                                view="outlined-danger"
                                size="l"
                                type="submit"
                                loading={processing}
                            >
                                Delete Account
                            </Button>
                        </div>
                    </form>
                </div>
            </Modal>
        </section>
    );
}
