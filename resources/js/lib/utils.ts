import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    const targetUrl = toUrl(urlToCheck);
    // Remove query parameters for comparison
    const currentPath = currentUrl.split('?')[0];
    const targetPath = targetUrl.split('?')[0];
    // Check if current path starts with target path (for nested routes)
    return currentPath === targetPath || currentPath.startsWith(targetPath + '/');
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

/**
 * Format date/time string to Jakarta timezone (WIB)
 * @param dateString - ISO date string or any parseable date
 * @returns Formatted date string in Indonesian locale
 */
export const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

/**
 * Format time only in Jakarta timezone
 */
export const formatTime = (dateString: string): string => {
    return new Date(dateString).toLocaleString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
    });
};

/**
 * Format date only (no time) in Jakarta timezone
 */
export const formatDateOnly = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
