import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import { Card, Text } from '@gravity-ui/uikit';

export default function Edit({
    mustVerifyEmail,
    status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
    return (
        <AuthenticatedLayout
            header={
                <Text variant="header-1">
                    Profile
                </Text>
            }
        >
            <Head title="Profile" />

            <div className="container">
                <Card className="mb-4">
                    <UpdateProfileInformationForm
                        mustVerifyEmail={mustVerifyEmail}
                        status={status}
                        className="w-100"
                    />
                </Card>

                <Card className="mb-4">
                    <UpdatePasswordForm className="w-100" />
                </Card>

                <Card>
                    <DeleteUserForm className="w-100" />
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
