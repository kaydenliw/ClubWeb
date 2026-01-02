# Confirmation Dialog Implementation Guide

## ✅ Confirmation Dialog System - READY TO USE!

The confirmation dialog system has been added to the dashboard layout and is ready to use across all pages.

## How to Use

### 1. **For Delete Actions**

Replace your delete button/form with this pattern:

**Before:**
```html
<form method="POST" action="{{ route('organization.members.destroy', $member) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Delete</button>
</form>
```

**After:**
```html
<form id="delete-member-{{ $member->id }}" method="POST" action="{{ route('organization.members.destroy', $member) }}">
    @csrf
    @method('DELETE')
</form>
<button onclick="return confirmDelete('delete-member-{{ $member->id }}', '{{ $member->name }}')">
    Delete
</button>
```

### 2. **For Bulk Actions**

**Example for bulk delete:**
```html
<form id="bulk-delete-form" method="POST" action="{{ route('organization.members.bulk-delete') }}">
    @csrf
    <!-- checkboxes here -->
</form>
<button onclick="return confirmBulkAction('bulk-delete-form', 'delete', getSelectedCount())">
    Delete Selected
</button>

<script>
function getSelectedCount() {
    return document.querySelectorAll('input[name="member_ids[]"]:checked').length;
}
</script>
```

### 3. **For Custom Confirmations**

```javascript
showConfirmDialog(
    'Confirm Action',           // Title
    'Are you sure?',            // Message
    function() {                // Callback function
        // Your action here
        document.getElementById('myForm').submit();
    },
    'Proceed',                  // Button text (optional)
    'blue'                      // Button color: red, blue, green, yellow (optional)
);
```

## Pages That Need Confirmations

### Organization Portal:

**High Priority (Delete Actions):**
- [ ] `/organization/members/index.blade.php` - Delete member
- [ ] `/organization/members/show.blade.php` - Delete member
- [ ] `/organization/charges/index.blade.php` - Delete charge
- [ ] `/organization/charges/show.blade.php` - Delete charge
- [ ] `/organization/announcements/index.blade.php` - Delete announcement
- [ ] `/organization/announcements/show.blade.php` - Delete announcement
- [ ] `/organization/faqs/index.blade.php` - Delete FAQ

**Medium Priority (Bulk Actions):**
- [ ] `/organization/members/index.blade.php` - Bulk delete, bulk status update

**Low Priority (Status Changes):**
- [ ] `/organization/members/edit.blade.php` - Change status to inactive
- [ ] `/organization/charges/edit.blade.php` - Change status

### Admin Portal:

**High Priority (Delete Actions):**
- [ ] `/admin/organizations/index.blade.php` - Delete organization
- [ ] `/admin/organizations/show.blade.php` - Delete organization

**Medium Priority (Critical Actions):**
- [ ] `/admin/settlements/create.blade.php` - Create settlement (involves money)
- [ ] `/admin/transactions/index.blade.php` - Sync to accounting

## Quick Implementation Examples

### Example 1: Members Index Page

Find the delete button and update it:

```html
<!-- Add this form (hidden) -->
<form id="delete-member-{{ $member->id }}"
      method="POST"
      action="{{ route('organization.members.destroy', $member) }}"
      style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Update the delete button -->
<button type="button"
        onclick="return confirmDelete('delete-member-{{ $member->id }}', '{{ $member->name }}')"
        class="text-red-600 hover:text-red-700">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
    </svg>
</button>
```

### Example 2: Bulk Delete

```html
<form id="bulk-delete-form" method="POST" action="{{ route('organization.members.bulk-delete') }}">
    @csrf
    <input type="hidden" name="member_ids" id="bulk-member-ids">
</form>

<button type="button"
        onclick="handleBulkDelete()"
        class="px-4 py-2 bg-red-600 text-white rounded-lg">
    Delete Selected
</button>

<script>
function handleBulkDelete() {
    const checkboxes = document.querySelectorAll('input[name="member_ids[]"]:checked');
    if (checkboxes.length === 0) {
        showToast('Please select at least one member', 'warning');
        return;
    }

    const ids = Array.from(checkboxes).map(cb => cb.value);
    document.getElementById('bulk-member-ids').value = JSON.stringify(ids);

    return confirmBulkAction('bulk-delete-form', 'delete', checkboxes.length);
}
</script>
```

### Example 3: Critical Status Change

```html
<button type="button"
        onclick="confirmStatusChange()"
        class="px-4 py-2 bg-yellow-600 text-white rounded-lg">
    Deactivate Member
</button>

<script>
function confirmStatusChange() {
    showConfirmDialog(
        'Confirm Status Change',
        'Deactivating this member will prevent them from accessing their account. Continue?',
        function() {
            document.getElementById('statusForm').submit();
        },
        'Deactivate',
        'yellow'
    );
}
</script>
```

## Testing Checklist

After implementing confirmations, test:
- [ ] Dialog appears when clicking delete/action button
- [ ] Cancel button closes dialog without action
- [ ] Confirm button executes the action
- [ ] Dialog shows correct item name/count
- [ ] Toast notification appears after successful action

## Notes

- The confirmation dialog is already styled and responsive
- It works with all existing forms - just add the onclick handler
- No additional CSS or JavaScript files needed
- Works on both organization and admin portals
