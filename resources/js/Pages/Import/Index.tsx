import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

export default function ImportIndex({
    success,
    error,
}: {
    success?: string;
    error?: string;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        file: null as File | null,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('import.store'), {
            onSuccess: () => reset('file'),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Import Conversations
                </h2>
            }
        >
            <Head title="Import" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {success && (
                        <div className="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                            {success}
                        </div>
                    )}

                    {error && (
                        <div className="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                            {error}
                        </div>
                    )}

                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-lg font-medium text-gray-900">
                                Upload Conversation Export
                            </h3>
                            <p className="mt-1 text-sm text-gray-600">
                                Import your ChatGPT or Claude conversation history by uploading the JSON export file.
                            </p>

                            <form onSubmit={submit} className="mt-6">
                                <div>
                                    <label
                                        htmlFor="file"
                                        className="block text-sm font-medium text-gray-700"
                                    >
                                        Select JSON File
                                    </label>
                                    <input
                                        id="file"
                                        type="file"
                                        accept=".json"
                                        onChange={(e) =>
                                            setData('file', e.target.files?.[0] || null)
                                        }
                                        className="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                    />
                                    <InputError message={errors.file} className="mt-2" />
                                </div>

                                <div className="mt-6 flex items-center gap-4">
                                    <PrimaryButton disabled={processing || !data.file}>
                                        {processing ? 'Importing...' : 'Import'}
                                    </PrimaryButton>
                                </div>
                            </form>

                            <div className="mt-8 border-t border-gray-200 pt-6">
                                <h4 className="text-sm font-medium text-gray-900">
                                    How to export your conversations:
                                </h4>
                                <ul className="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600">
                                    <li>
                                        <strong>ChatGPT:</strong> Settings → Data Controls → Export Data
                                    </li>
                                    <li>
                                        <strong>Claude:</strong> Settings → Export Data
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
