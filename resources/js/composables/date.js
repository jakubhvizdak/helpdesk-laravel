export const formatDate = (dateStr) => {
    if (!dateStr) return null;
    const d = new Date(dateStr);
    if (isNaN(d)) return null;
    return d.toISOString().split('T')[0];
};

export const formatDateTime = (dateStr) => {
    if (!dateStr) return null;
    const d = new Date(dateStr);
    if (isNaN(d)) return null;

    const pad = (n) => n.toString().padStart(2, '0');

    const year = d.getFullYear();
    const month = pad(d.getMonth() + 1);
    const day = pad(d.getDate());
    const hours = pad(d.getHours());
    const minutes = pad(d.getMinutes());

    return `${year}-${month}-${day} ${hours}:${minutes}`;
};

export const formatDateDifference = (dateStr) => {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const now = new Date();
    const diffMins = Math.floor((now - d) / 60000);
    if (diffMins < 1) return 'Teraz';
    if (diffMins < 60) return `Pred ${diffMins} min`;
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `Pred ${diffHours} h`;
    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return `Pred ${diffDays} d`;
    return d.toLocaleDateString('sk-SK', { day: '2-digit', month: '2-digit' }) + ' ' +
        d.toLocaleTimeString('sk-SK', { hour: '2-digit', minute: '2-digit' });
};


export const getTodayDate = () => {
    const d = new Date();
    const days = ["Nedeľa","Pondelok","Utorok","Streda","Štvrtok","Piatok","Sobota"];
    return `${days[d.getDay()]}, ${d.getDate()}.${d.getMonth() + 1}.${d.getFullYear()}`;
};

export const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return "Dobré ráno";
    if (hour < 18) return "Dobré popoludnie";
    return "Dobrý večer";
};

export function formatDateLabel(date) {
    if (!date) return '';
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    return `${day}/${month}`;
}
