/** Produce an action result without giving the runner any mutation capability. */
export function simulateAction(stream, action, entry) {
    if (!stream.actions.some((candidate) => candidate.handle === action)) {
        throw new Error('Unknown playground action.');
    }
    if (!stream.entries.some((candidate) => candidate.id === entry.id)) {
        throw new Error('Unknown playground entry.');
    }

    const before = structuredClone(entry);
    let after = structuredClone(entry);
    let message;

    switch (action) {
        case 'create':
            after.id = '(new ID would be generated)';
            message = `Would create a new ${stream.name.toLowerCase()} entry from these values.`;
            break;
        case 'update':
            message = `Would save the current values for ${entry.id}.`;
            break;
        case 'delete':
            after = null;
            message = `Would delete ${entry.id}.`;
            break;
        case 'invite':
            message = `Would send an invitation to ${entry.email}.`;
            break;
        case 'publish':
            after.status = 'published';
            message = `Would publish “${entry.title}”.`;
            break;
        case 'refund':
            after.status = 'refunded';
            message = `Would refund USD ${entry.total} for ${entry.id}.`;
            break;
        default:
            throw new Error('Unsupported playground action.');
    }

    return {
        simulated: true,
        action,
        message: `${message} Simulation only; nothing was changed or sent.`,
        before: action === 'create' ? null : before,
        after,
    };
}
