---
title: Forms and Inputs
description: 'Creating dynamic forms with Streams UI.'
sort_order: 1
category: core-concepts
status: live
---

# Forms and Inputs

Streams UI provides powerful form generation capabilities that automatically create forms based on your stream field definitions, with extensive customization options.

## Basic Form Generation

### Automatic Forms

Generate forms directly from stream configurations:

```php
// Simple form for a stream
{!! UI::form('users')->render() !!}

// Form with specific fields
{!! UI::form('users')->fields(['name', 'email', 'role'])->render() !!}

// Form for editing an entry
{!! UI::form('users')->entry($user)->render() !!}
```

### Form Builder

Use the form builder for more control:

```php
$form = UI::form('users')
    ->fields(['name', 'email', 'password'])
    ->buttons(['save', 'cancel'])
    ->action('/users')
    ->method('POST')
    ->layout('vertical');

return $form->render();
```

## Form Configuration

### Stream-Level Configuration

Configure forms in your stream definition:

```json
// streams/users.json
{
    "fields": [
        {
            "handle": "name",
            "type": "string",
            "input": {
                "placeholder": "Enter full name",
                "help": "First and last name"
            }
        },
        {
            "handle": "email", 
            "type": "email",
            "input": {
                "icon": "envelope",
                "validation": "live"
            }
        },
        {
            "handle": "role",
            "type": "select",
            "input": {
                "options": {
                    "user": "Regular User",
                    "admin": "Administrator",
                    "moderator": "Moderator"
                }
            }
        }
    ],
    "ui": {
        "forms": [
            {
                "handle": "default",
                "layout": "horizontal",
                "sections": [
                    {
                        "title": "Basic Information",
                        "fields": ["name", "email"]
                    },
                    {
                        "title": "Access",
                        "fields": ["role", "active"]
                    }
                ]
            }
        ]
    }
}
```

### Programmatic Configuration

Configure forms in PHP:

```php
$form = UI::form('users')
    ->layout('horizontal')
    ->section('Basic Info', ['name', 'email'])
    ->section('Settings', ['role', 'active'])
    ->button('save', [
        'text' => 'Save User',
        'type' => 'primary'
    ])
    ->button('cancel', [
        'text' => 'Cancel',
        'href' => '/admin/users'
    ]);
```

## Form Layouts

### Vertical Layout (Default)

```php
$form = UI::form('users')->layout('vertical');
```

```html
<div class="form-vertical">
    <div class="field">
        <label>Name</label>
        <input type="text" name="name">
    </div>
    <div class="field">
        <label>Email</label>
        <input type="email" name="email">
    </div>
</div>
```

### Horizontal Layout

```php
$form = UI::form('users')->layout('horizontal');
```

```html
<div class="form-horizontal">
    <div class="field flex">
        <label class="w-1/4">Name</label>
        <div class="w-3/4">
            <input type="text" name="name">
        </div>
    </div>
</div>
```

### Grid Layout

```php
$form = UI::form('users')->layout('grid', [
    'columns' => 2,
    'gap' => 'md'
]);
```

### Custom Layout

```php
$form = UI::form('users')->layout('custom', [
    'template' => 'forms.custom-layout'
]);
```

## Form Sections and Tabs

### Sections

Organize fields into sections:

```php
$form = UI::form('users')
    ->section('Personal', ['name', 'email', 'phone'])
    ->section('Professional', ['company', 'title', 'department'])
    ->section('Settings', ['role', 'active', 'notifications']);
```

### Tabs

Use tabs for complex forms:

```php
$form = UI::form('users')
    ->tab('Profile', ['name', 'email', 'bio'])
    ->tab('Security', ['password', 'two_factor', 'permissions'])
    ->tab('Preferences', ['timezone', 'language', 'theme']);
```

### Accordion Sections

Collapsible sections:

```php
$form = UI::form('users')
    ->accordion('Basic Info', ['name', 'email'], ['open' => true])
    ->accordion('Advanced', ['role', 'permissions'], ['open' => false]);
```

## Input Types

### Text Inputs

```php
// Basic text input
UI::input('name')->type('text')

// Text with placeholder and help
UI::input('name')
    ->placeholder('Enter your full name')
    ->help('First and last name required')

// Text with icon
UI::input('username')
    ->icon('user')
    ->prefix('@')
```

### Email and URL Inputs

```php
// Email input with validation
UI::input('email')
    ->type('email')
    ->validate('email:rfc,dns')

// URL input
UI::input('website')
    ->type('url')
    ->placeholder('https://example.com')
```

### Password Inputs

```php
// Password with confirmation
UI::input('password')
    ->type('password')
    ->confirmation(true)
    ->strength(true)

// Password with toggle visibility
UI::input('password')
    ->type('password')
    ->toggle(true)
```

### Number Inputs

```php
// Number with min/max
UI::input('age')
    ->type('number')
    ->min(18)
    ->max(120)
    ->step(1)

// Currency input
UI::input('price')
    ->type('currency')
    ->currency('USD')
    ->decimals(2)
```

### Date and Time Inputs

```php
// Date picker
UI::input('birth_date')
    ->type('date')
    ->format('Y-m-d')
    ->min('1900-01-01')

// DateTime picker
UI::input('appointment')
    ->type('datetime')
    ->format('Y-m-d H:i')

// Time picker
UI::input('start_time')
    ->type('time')
    ->format('H:i')
```

### Textarea

```php
// Basic textarea
UI::input('description')
    ->type('textarea')
    ->rows(5)

// Rich text editor
UI::input('content')
    ->type('editor')
    ->toolbar(['bold', 'italic', 'link'])
```

### Select Inputs

```php
// Basic select
UI::input('role')
    ->type('select')
    ->options([
        'user' => 'User',
        'admin' => 'Administrator'
    ])

// Select with search
UI::input('country')
    ->type('select')
    ->searchable(true)
    ->options($countries)

// Multiple select
UI::input('skills')
    ->type('multiselect')
    ->options($skillOptions)
```

### Checkbox and Radio

```php
// Single checkbox
UI::input('subscribe')
    ->type('checkbox')
    ->label('Subscribe to newsletter')

// Checkbox group
UI::input('permissions')
    ->type('checkboxes')
    ->options([
        'read' => 'Read',
        'write' => 'Write',
        'delete' => 'Delete'
    ])

// Radio buttons
UI::input('gender')
    ->type('radio')
    ->options([
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other'
    ])
```

### File Uploads

```php
// Basic file upload
UI::input('document')
    ->type('file')
    ->accept('.pdf,.doc,.docx')

// Image upload with preview
UI::input('avatar')
    ->type('image')
    ->preview(true)
    ->resize('300x300')

// Multiple file upload
UI::input('attachments')
    ->type('files')
    ->multiple(true)
    ->max(5)
```

## Form Validation

### Client-Side Validation

```php
// Live validation
UI::input('email')
    ->validate('email')
    ->live(true)

// Custom validation rules
UI::input('username')
    ->validate(['required', 'min:3', 'unique:users,username'])
    ->messages([
        'unique' => 'This username is already taken.'
    ])
```

### Form-Level Validation

```php
$form = UI::form('users')
    ->rules([
        'name' => 'required|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed'
    ])
    ->messages([
        'email.unique' => 'This email is already registered.'
    ]);
```

### Conditional Validation

```php
// Validate based on other field values
UI::input('other_reason')
    ->validate('required_if:reason,other')
    ->showIf('reason', 'other')
```

## Form Buttons

### Standard Buttons

```php
$form = UI::form('users')
    ->button('save', [
        'text' => 'Save',
        'type' => 'primary',
        'icon' => 'check'
    ])
    ->button('cancel', [
        'text' => 'Cancel',
        'type' => 'secondary',
        'href' => '/admin/users'
    ]);
```

### Custom Buttons

```php
$form = UI::form('users')
    ->button('save_and_continue', [
        'text' => 'Save & Continue',
        'type' => 'primary',
        'action' => 'save',
        'redirect' => '/admin/users/create'
    ])
    ->button('delete', [
        'text' => 'Delete',
        'type' => 'danger',
        'confirm' => 'Are you sure?',
        'showIf' => 'id'
    ]);
```

## Advanced Features

### Conditional Fields

Show/hide fields based on other field values:

```php
UI::input('employment_type')
    ->type('select')
    ->options(['full-time', 'part-time', 'contract'])

UI::input('contract_end_date')
    ->type('date')
    ->showIf('employment_type', 'contract')

UI::input('hourly_rate')
    ->type('currency')
    ->showIf('employment_type', ['part-time', 'contract'])
```

### Field Dependencies

Create dependencies between fields:

```php
UI::input('country')
    ->type('select')
    ->options($countries)
    ->onChange('loadStates')

UI::input('state')
    ->type('select')
    ->dependsOn('country')
    ->source('/api/states/{country}')
```

### Dynamic Fields

Add/remove fields dynamically:

```php
UI::input('skills')
    ->type('repeater')
    ->fields([
        'name' => ['type' => 'text'],
        'level' => ['type' => 'select', 'options' => $levels]
    ])
    ->min(1)
    ->max(10)
```

### Form Wizards

Multi-step forms:

```php
$wizard = UI::wizard('user-registration')
    ->step('Personal', ['name', 'email', 'phone'])
    ->step('Professional', ['company', 'title'])
    ->step('Preferences', ['timezone', 'notifications'])
    ->step('Review', [], ['template' => 'forms.review']);
```

## Form Events

### JavaScript Events

```javascript
// Form submission
document.addEventListener('form:submitting', function(e) {
    console.log('Form submitting:', e.detail.form);
});

// Field changes
document.addEventListener('field:changed', function(e) {
    console.log('Field changed:', e.detail.field, e.detail.value);
});

// Validation events
document.addEventListener('field:validated', function(e) {
    console.log('Field validated:', e.detail.field, e.detail.valid);
});
```

### PHP Events

```php
// Form building event
Event::listen('form.building:users', function ($form) {
    $form->field('created_by', [
        'type' => 'hidden',
        'value' => auth()->id()
    ]);
});

// Form saving event
Event::listen('form.saving:users', function ($form, $entry) {
    if (!$entry->exists) {
        $entry->created_by = auth()->id();
    }
});
```

## Theming and Styling

### CSS Classes

Customize form appearance:

```php
$form = UI::form('users')
    ->class('form-custom')
    ->fieldClass('field-custom')
    ->labelClass('label-custom')
    ->inputClass('input-custom');
```

### Custom Templates

Use custom Blade templates:

```php
$form = UI::form('users')
    ->template('forms.custom')
    ->fieldTemplate('forms.fields.custom')
    ->buttonTemplate('forms.buttons.custom');
```

### Theme Configuration

Configure themes in config:

```php
// config/streams/ui.php
'forms' => [
    'theme' => 'bootstrap',
    'classes' => [
        'form' => 'form',
        'field' => 'form-group',
        'label' => 'form-label',
        'input' => 'form-control',
        'button' => 'btn',
    ],
];
```

## Best Practices

1. **Use stream configurations**: Define forms in stream JSON when possible
2. **Validate early**: Implement client-side validation for better UX
3. **Group related fields**: Use sections and tabs for complex forms
4. **Provide clear labels**: Use descriptive field labels and help text
5. **Handle errors gracefully**: Display validation errors clearly
6. **Optimize for mobile**: Ensure forms work well on mobile devices
7. **Use appropriate input types**: Choose the right input type for each field
8. **Test accessibility**: Ensure forms are accessible to all users
9. **Cache form configurations**: Cache complex form definitions
10. **Monitor performance**: Profile form rendering and submission times

## Examples

### User Registration Form

```php
$form = UI::form('users')
    ->layout('vertical')
    ->section('Account', [
        'name' => ['required' => true, 'placeholder' => 'Full Name'],
        'email' => ['required' => true, 'type' => 'email'],
        'password' => ['required' => true, 'type' => 'password', 'confirmation' => true]
    ])
    ->section('Profile', [
        'bio' => ['type' => 'textarea', 'rows' => 3],
        'avatar' => ['type' => 'image', 'preview' => true]
    ])
    ->button('register', ['text' => 'Create Account', 'type' => 'primary'])
    ->button('login', ['text' => 'Already have an account?', 'href' => '/login']);
```

### Product Management Form

```php
$form = UI::form('products')
    ->tab('Basic Info', [
        'name' => ['required' => true],
        'sku' => ['required' => true, 'unique' => true],
        'category' => ['type' => 'relationship', 'related' => 'categories']
    ])
    ->tab('Pricing', [
        'price' => ['type' => 'currency', 'required' => true],
        'sale_price' => ['type' => 'currency'],
        'tax_class' => ['type' => 'select', 'options' => $taxClasses]
    ])
    ->tab('Inventory', [
        'track_inventory' => ['type' => 'checkbox'],
        'stock_quantity' => ['type' => 'number', 'showIf' => 'track_inventory:true'],
        'low_stock_threshold' => ['type' => 'number', 'showIf' => 'track_inventory:true']
    ])
    ->tab('Media', [
        'images' => ['type' => 'images', 'multiple' => true],
        'gallery' => ['type' => 'files', 'accept' => 'image/*']
    ]);
```
