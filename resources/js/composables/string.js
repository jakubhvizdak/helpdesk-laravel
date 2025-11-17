export const getInitials = (name = '', surname = '') => {
    const n = (name || '').trim();
    const s = (surname || '').trim();
    if (!n && !s) return 'U';
    return ((n[0] || '') + (s[0] || '')).toUpperCase();
};

export const formatCurrency = (value) => {
    if (value == null) return '-';
    return new Intl.NumberFormat('sk-SK', { style: 'currency', currency: 'EUR', minimumFractionDigits: 2 }).format(value);
};
