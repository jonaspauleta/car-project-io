import { 
    Pagination as PaginationRoot,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
    PaginationEllipsis
} from '@/components/ui/pagination';
import { router } from '@inertiajs/react';
import { type PaginatedResponse } from '@/types';

interface PaginationProps<T> {
    pagination: PaginatedResponse<T>;
    className?: string;
}

export default function Pagination<T>({ pagination, className = '' }: PaginationProps<T>) {
    // Defensive check for pagination structure
    if (!pagination || !pagination.meta || !pagination.links) {
        return null;
    }

    const { meta, links } = pagination;
    
    if (!meta.last_page || meta.last_page <= 1) {
        return null;
    }

    const handlePageChange = (url: string) => {
        router.get(url, {}, {
            preserveState: true,
            replace: true,
        });
    };

    // Generate page numbers to display
    const generatePageNumbers = () => {
        const current = meta.current_page;
        const last = meta.last_page;
        const pages = [];

        // Always show first page
        pages.push(1);

        // Show ellipsis if there's a gap after page 1
        if (current > 4) {
            pages.push('ellipsis-start');
        }

        // Show pages around current page
        const start = Math.max(2, current - 1);
        const end = Math.min(last - 1, current + 1);

        for (let i = start; i <= end; i++) {
            if (i !== 1 && i !== last) {
                pages.push(i);
            }
        }

        // Show ellipsis if there's a gap before last page
        if (current < last - 3) {
            pages.push('ellipsis-end');
        }

        // Always show last page (if it's not page 1)
        if (last > 1) {
            pages.push(last);
        }

        return pages;
    };

    const pageNumbers = generatePageNumbers();

    return (
        <div className={`space-y-4 ${className}`}>
            <div className="flex items-center justify-between text-sm text-muted-foreground">
                <p>
                    Showing {meta.from || 0} to {meta.to || 0} of {meta.total || 0} results
                </p>
            </div>
            
            <PaginationRoot>
                <PaginationContent>
                    {links.prev && (
                        <PaginationItem>
                            <PaginationPrevious 
                                href="#"
                                size="default"
                                onClick={(e) => {
                                    e.preventDefault();
                                    handlePageChange(links.prev!);
                                }}
                            />
                        </PaginationItem>
                    )}
                    
                    {pageNumbers.map((page, index) => {
                        if (page === 'ellipsis-start' || page === 'ellipsis-end') {
                            return (
                                <PaginationItem key={`ellipsis-${index}`}>
                                    <PaginationEllipsis />
                                </PaginationItem>
                            );
                        }
                        
                        const pageNum = page as number;
                        const isActive = pageNum === meta.current_page;
                        
                        // Find the URL for this page number
                        const pageUrl = Object.keys(links).find(key => {
                            if (key === 'prev' || key === 'next') return false;
                            const url = (links as any)[key];
                            if (!url) return false;
                            // Extract page number from URL
                            const match = url.match(/[?&]page=(\d+)/);
                            return match && parseInt(match[1]) === pageNum;
                        });
                        
                        return (
                            <PaginationItem key={pageNum}>
                                <PaginationLink
                                    href="#"
                                    isActive={isActive}
                                    size="icon"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        if (pageUrl && (links as any)[pageUrl]) {
                                            handlePageChange((links as any)[pageUrl]!);
                                        }
                                    }}
                                >
                                    {pageNum}
                                </PaginationLink>
                            </PaginationItem>
                        );
                    })}
                    
                    {links.next && (
                        <PaginationItem>
                            <PaginationNext 
                                href="#"
                                size="default"
                                onClick={(e) => {
                                    e.preventDefault();
                                    handlePageChange(links.next!);
                                }}
                            />
                        </PaginationItem>
                    )}
                </PaginationContent>
            </PaginationRoot>
        </div>
    );
}
