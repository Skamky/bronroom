import {Button, Label, Text, TextInput} from '@gravity-ui/uikit';
import GuestLayout from '@/Layouts/GuestLayout';
import {Head, useForm} from '@inertiajs/react';
import {FormEventHandler} from 'react';

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('password.confirm'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Confirm Password" />

            <Text className="mb-4">
                This is a secure area of the application. Please confirm your
                password before continuing.
            </Text>

            <form onSubmit={submit}>
                <div className="mt-4">
                    <Label>Password</Label>

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        size="l"
                        autoFocus={true}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
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
                        Confirm
                    </Button>
                </div>
            </form>
        </GuestLayout>
    );
}
