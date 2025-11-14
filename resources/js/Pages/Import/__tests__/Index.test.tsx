import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen } from '@testing-library/react';
import ImportIndex from '../Index';

// Mock Inertia modules
vi.mock('@inertiajs/react', () => ({
    Head: ({ title }: { title: string }) => <title>{title}</title>,
    useForm: () => ({
        data: { file: null },
        setData: vi.fn(),
        post: vi.fn(),
        processing: false,
        errors: {},
        reset: vi.fn(),
    }),
    router: {
        post: vi.fn(),
    },
}));

// Mock AuthenticatedLayout
vi.mock('@/Layouts/AuthenticatedLayout', () => ({
    default: ({ children, header }: any) => (
        <div>
            <div data-testid="header">{header}</div>
            <div data-testid="content">{children}</div>
        </div>
    ),
}));

// Mock components
vi.mock('@/Components/InputError', () => ({
    default: ({ message }: { message?: string }) =>
        message ? <div role="alert">{message}</div> : null,
}));

vi.mock('@/Components/PrimaryButton', () => ({
    default: ({ children, disabled }: any) => (
        <button disabled={disabled}>{children}</button>
    ),
}));

describe('Import Page', () => {
    it('renders the import page title', () => {
        render(<ImportIndex />);
        expect(screen.getByText('Import Conversations')).toBeInTheDocument();
    });

    it('renders upload instructions', () => {
        render(<ImportIndex />);
        expect(screen.getByText('Upload Conversation Export')).toBeInTheDocument();
        expect(
            screen.getByText(/Import your ChatGPT or Claude conversation history/)
        ).toBeInTheDocument();
    });

    it('renders file input', () => {
        render(<ImportIndex />);
        const fileInput = screen.getByLabelText('Select JSON File');
        expect(fileInput).toBeInTheDocument();
        expect(fileInput).toHaveAttribute('type', 'file');
        expect(fileInput).toHaveAttribute('accept', '.json');
    });

    it('renders import button', () => {
        render(<ImportIndex />);
        const button = screen.getByRole('button', { name: /import/i });
        expect(button).toBeInTheDocument();
    });

    it('shows ChatGPT export instructions', () => {
        render(<ImportIndex />);
        expect(screen.getByText(/ChatGPT:/)).toBeInTheDocument();
        expect(
            screen.getByText(/Settings → Data Controls → Export Data/)
        ).toBeInTheDocument();
    });

    it('shows Claude export instructions', () => {
        render(<ImportIndex />);
        expect(screen.getByText(/Claude:/)).toBeInTheDocument();
        expect(screen.getByText(/Settings → Export Data/)).toBeInTheDocument();
    });

    it('displays success message when provided', () => {
        render(<ImportIndex success="Successfully imported 5 conversations!" />);
        expect(
            screen.getByText('Successfully imported 5 conversations!')
        ).toBeInTheDocument();
    });

    it('displays error message when provided', () => {
        render(<ImportIndex error="Import failed: Invalid JSON" />);
        expect(screen.getByText('Import failed: Invalid JSON')).toBeInTheDocument();
    });

    it('does not display success message when not provided', () => {
        render(<ImportIndex />);
        expect(screen.queryByText(/Successfully imported/)).not.toBeInTheDocument();
    });

    it('does not display error message when not provided', () => {
        render(<ImportIndex />);
        expect(screen.queryByText(/Import failed/)).not.toBeInTheDocument();
    });
});
