import { Head } from '@inertiajs/react';

export default function Welcome() {
    return (
        <>
            <Head title="Welcome" />
            <div className="min-h-screen bg-gray-100 flex items-center justify-center">
                <div className="max-w-md w-full bg-white shadow-lg rounded-lg p-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-4">
                        AI Chat Organizer
                    </h1>
                    <p className="text-gray-600">
                        Your AI conversations, organized and searchable.
                    </p>
                    <div className="mt-6 text-sm text-gray-500">
                        Laravel 12 • React 19 • TypeScript • Tailwind 4
                    </div>
                </div>
            </div>
        </>
    );
}
