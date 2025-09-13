import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { format } from 'date-fns';
import { Calendar as CalendarIcon } from 'lucide-react';
import { toast } from 'sonner';

import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';
import { index as tokensIndex, store, destroy, revokeAll } from '@/routes/tokens';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';

import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';

interface Token {
    id: number;
    name: string;
    last_used_at: string | null;
    expires_at: string | null;
    created_at: string;
}

interface Props {
    tokens: Token[];
    flash?: {
        message?: string;
        token?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'API Tokens',
        href: tokensIndex().url,
    },
];

export default function Tokens({ tokens, flash }: Props) {
    const [showToken, setShowToken] = useState(false);
    const [newToken, setNewToken] = useState('');
    const [isCreating, setIsCreating] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        expires_at: '',
    });
    const [selectedDate, setSelectedDate] = useState<Date | undefined>();

    // Handle token-specific flash logic
    useEffect(() => {
        if (flash?.token) {
            setNewToken(flash.token);
            setShowToken(true);
        }
    }, [flash]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsCreating(true);
        
        const submitData = {
            name: formData.name,
            expires_at: selectedDate ? format(selectedDate, 'yyyy-MM-dd\'T\'HH:mm') : '',
        };
        
        router.post(store().url, submitData, {
            onFinish: () => setIsCreating(false),
        });
    };

    const handleDelete = (tokenId: number) => {
        if (confirm('Are you sure you want to delete this token?')) {
            router.delete(destroy({ token: tokenId }).url);
        }
    };

    const handleRevokeAll = () => {
        if (confirm('Are you sure you want to revoke all tokens? This action cannot be undone.')) {
            router.delete(revokeAll().url);
        }
    };

    const copyToken = () => {
        navigator.clipboard.writeText(newToken);
        toast.success('Token copied to clipboard!');
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="API Tokens" />

            <SettingsLayout>
                <div className="space-y-6">
                    <div className="flex items-center justify-between">
                        <HeadingSmall 
                            title="API Tokens" 
                            description="Manage your API tokens for accessing the application programmatically" 
                        />
                        {tokens.length > 0 && (
                            <Button
                                onClick={handleRevokeAll}
                                variant="destructive"
                                size="sm"
                            >
                                Revoke All Tokens
                            </Button>
                        )}
                    </div>

                    {showToken && newToken && (
                        <Card>
                            <CardHeader>
                                <CardTitle>Your New API Token</CardTitle>
                                <CardDescription>
                                    Please copy your new API token now. You won't be able to see it again!
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="flex items-center gap-2">
                                    <code className="flex-1 rounded bg-muted px-3 py-2 text-sm font-mono">
                                        {newToken}
                                    </code>
                                    <Button
                                        onClick={copyToken}
                                        variant="outline"
                                        size="sm"
                                    >
                                        Copy
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    <Card>
                        <CardHeader>
                            <CardTitle>Create New Token</CardTitle>
                            <CardDescription>
                                Create a new API token to access the application programmatically.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Token name</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        required
                                        value={formData.name}
                                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                        placeholder="Enter token name"
                                    />
                                </div>
                                <div className="grid gap-2">
                                    <Label>Expires at (optional)</Label>
                                    <Popover>
                                        <PopoverTrigger asChild>
                                            <Button
                                                variant="outline"
                                                data-empty={!selectedDate}
                                                className={cn(
                                                    "w-full justify-start text-left font-normal",
                                                    !selectedDate && "text-muted-foreground"
                                                )}
                                            >
                                                <CalendarIcon className="mr-2 h-4 w-4" />
                                                {selectedDate ? format(selectedDate, "PPP") : <span>Pick a date</span>}
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent className="w-56 p-0" align="end">
                                            <Calendar
                                                className="rounded-md border-0 w-full"
                                                mode="single"
                                                selected={selectedDate}
                                                onSelect={setSelectedDate}
                                                disabled={(date) => date < new Date()}
                                            />
                                        </PopoverContent>
                                    </Popover>
                                </div>
                                <Button
                                    type="submit"
                                    variant="default"
                                    disabled={isCreating || !formData.name.trim()}
                                >
                                    {isCreating ? 'Creating...' : 'Create Token'}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>

                    {tokens.length > 0 && (
                        <Card>
                            <CardHeader>
                                <CardTitle>Existing Tokens</CardTitle>
                                <CardDescription>
                                    Manage your existing API tokens. You can revoke any token at any time.
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    {tokens.map((token) => (
                                        <div
                                            key={token.id}
                                            className="flex items-center justify-between rounded-lg border p-4"
                                        >
                                            <div className="space-y-1">
                                                <p className="font-medium">{token.name}</p>
                                                <div className="text-sm text-muted-foreground">
                                                    <p>Last used: {token.last_used_at || 'Never'}</p>
                                                    <p>Expires: {token.expires_at || 'Never'}</p>
                                                    <p>Created: {new Date(token.created_at).toLocaleDateString()}</p>
                                                </div>
                                            </div>
                                            <Button
                                                onClick={() => handleDelete(token.id)}
                                                variant="destructive"
                                                size="sm"
                                            >
                                                Delete
                                            </Button>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}