---
name: krayin-authorization
description: Understand, implement, debug, review, and test Krayin CRM branch 2.2 authorization. Covers ACL permissions, roles, Bouncer middleware, route-to-permission mapping, view permissions, data scoping, role privilege ceilings, user authorization, and authorization security testing.
license: MIT
metadata:
  author: webkul
---

# Krayin CRM 2.2 Authorization Skill

## Purpose

This skill defines the authorization architecture used by Krayin CRM
branch 2.2.

Use this skill whenever a task involves:

* ACL
* permissions
* roles
* authorization
* access control
* Bouncer
* route permissions
* role management
* user role assignment
* view permissions
* global/group/individual access
* data scoping
* privilege escalation
* authorization bugs
* security testing
* permission-related UI
* authorization middleware
* authorization regression tests

The authorization model has two orthogonal dimensions:

1. Action authorization through ACL.
2. Record/data authorization through view permission.

A user must satisfy both dimensions when an operation requires
record-level authorization.

---

# 0. Verified Source Anchors

These paths and symbols exist in branch 2.2 and were verified against the
source. Start here rather than searching blind. Re-verify before relying on
any behavioral detail — this section is an index, not a substitute for
reading the code.

| Concern | Location |
| ------- | -------- |
| ACL permission vocabulary | `packages/Webkul/Admin/src/Config/acl.php` |
| Permission tree + route map | `packages/Webkul/Core/src/Acl.php` |
| Request-level enforcement | `packages/Webkul/Admin/src/Http/Middleware/Bouncer.php` |
| Permission + scope helpers | `packages/Webkul/Admin/src/Bouncer.php` |
| `bouncer()` helper | `packages/Webkul/Admin/src/Http/helpers.php` |
| `acl()` helper | `packages/Webkul/Core/src/Http/helpers.php` |
| Menu ACL filtering | `packages/Webkul/Core/src/Menu.php` |
| Role model / permissions cast | `packages/Webkul/User/src/Models/Role.php` |
| User model / `hasPermission()` | `packages/Webkul/User/src/Models/User.php` |
| Group member ID resolution | `packages/Webkul/User/src/Repositories/UserRepository.php` |
| Record + collection scope guards | `packages/Webkul/Admin/src/Http/Controllers/Controller.php` |
| Role escalation guards | `packages/Webkul/Admin/src/Http/Controllers/Settings/RoleController.php` |
| Role/scope assignment guards | `packages/Webkul/Admin/src/Http/Controllers/Settings/UserController.php` |
| Menu vocabulary | `packages/Webkul/Admin/src/Config/menu.php` |
| Administrator role seed | `packages/Webkul/Installer/src/Database/Seeders/User/RoleSeeder.php` |

Key symbols to grep for:

```text
Acl::getItems()
Acl::getRoles()
Acl::getAuthorizedItems()
Bouncer::hasPermission()
Bouncer::allow()
Bouncer::getAuthorizedUserIds()
Middleware\Bouncer::isPermissionsEmpty()
Middleware\Bouncer::checkIfAuthorized()
Middleware\Bouncer::isAllowlistedRoute()
Middleware\Bouncer::isAuthorizedByFeature()
Controller::preventUnauthorizedAccess()
Controller::filterAuthorizedRecords()
RoleController::canManageRole()
UserController::preventUnauthorizedRoleAssignment()
UserController::preventUnauthorizedScopeAssignment()
UserRepository::getCurrentUserGroupsUserIds()
User::hasPermission()
```

---

# 1. Authorization Model

Krayin authorization is not represented by a single permission field.

There are two separate authorization dimensions.

## Action authorization

Stored primarily through the user's role:

```text
roles.permission_type
roles.permissions
```

The role determines which actions the user can perform.

Examples:

```text
leads
leads.view
leads.create
leads.edit
leads.delete

settings.user.roles.create
settings.user.roles.edit
settings.user.roles.delete
```

## Data authorization

Stored on the user:

```text
users.view_permission
```

Supported values:

```text
global
group
individual
```

This determines which records the user may access.

Therefore:

```text
Effective authorization =
    Action permission
    AND
    Authorized data scope
```

Do not treat ACL permission and view permission as interchangeable.

---

# 2. Core Authorization Tables

The primary database structures involved in authorization are:

| Table       | Important fields                             | Purpose                                       |
| ----------- | -------------------------------------------- | --------------------------------------------- |
| roles       | permission_type, permissions, created_by     | Defines role-level action permissions         |
| users       | role_id, view_permission, status, created_by | Associates user with role and data scope      |
| groups      | id, name                                     | Defines groups used by group-level visibility |
| user_groups | user_id, group_id                            | Associates users with groups                  |

Before changing authorization behavior, inspect the actual
branch 2.2 migrations/models/controllers rather than assuming
the schema.

---

# 3. ACL Permission Vocabulary

The ACL permission vocabulary is defined in:

```text
packages/Webkul/Admin/src/Config/acl.php
```

The ACL configuration contains permission entries.

Typical structure:

```php
[
    'key'   => 'leads.edit',
    'name'  => 'admin::app.acl.edit',
    'route' => [
        'admin.leads.edit',
        'admin.leads.update',
        'admin.leads.mass_update',
    ],
    'sort'  => 3,
],
```

Important:

The permission key and route name are not necessarily the same thing.

The ACL configuration maps routes to permission keys.

Always inspect the existing configuration before adding a new
permission.

Do not invent a new naming convention if an established Krayin
convention already exists.

---

# 4. ACL Source of Truth

The ACL configuration is consumed by the core ACL implementation.

Important concepts:

```text
acl.php
   |
   +-- permission tree
   |
   +-- route -> permission mapping
```

The permission tree is used by role-management UI.

The route mapping is used by request authorization.

When debugging authorization, determine:

1. What permission key is required?
2. Which route is being requested?
3. Is that route mapped?
4. Which role permissions does the user have?
5. Is the user an administrator?

---

# 5. Bouncer Middleware

Admin authorization is enforced by:

```text
packages/Webkul/Admin/src/Http/Middleware/Bouncer.php
```

The middleware is the authoritative request-level security boundary.

Do not assume that hiding a button or menu item provides security.

A request must be protected server-side.

Conceptually:

```text
Request
   |
   v
Authentication
   |
   v
User status
   |
   v
Role
   |
   v
Administrator check
   |
   v
Permission resolution
   |
   v
ACL authorization
   |
   +-- denied -> 401
   |
   v
Controller
```

The exact implementation must always be verified against
the current Krayin branch before modifying authorization code.

---

# 6. Administrator Behavior

Krayin supports an administrator/full-permission role through:

```text
permission_type = all
```

When working with administrator authorization:

* inspect the administrator short-circuit in middleware;
* inspect how `hasPermission()` handles `all`;
* inspect how role permissions are represented;
* do not assume that `permissions = null` or an empty array has
  the same meaning in every authorization method.

Important:

Different authorization entry points may implement administrator
handling differently.

Concrete example in branch 2.2: the seeded administrator role has
`permission_type = 'all'` and `permissions = null`. The instance method
`Admin\Bouncer::hasPermission()` checks `permission_type == 'all'` and
returns true, but the static `Bouncer::allow()` does not — it delegates
straight to `User::hasPermission()`, which reads `role->permissions`
directly. That path is only safe because the middleware short-circuits
administrators in `isPermissionsEmpty()` before `checkIfAuthorized()`
ever calls `allow()`.

When reviewing authorization code, verify the actual execution path
rather than relying on the value of `permission_type` alone.

---

# 7. Route Authorization

Authorization should be understood as:

```text
HTTP request
     |
     v
Route name
     |
     v
ACL route mapping
     |
     v
Permission key
     |
     v
User role permissions
     |
     v
Allow / Deny
```

Example:

```text
admin.leads.update
        |
        v
leads.edit
        |
        v
user role contains leads.edit?
        |
     +--+--+
    YES    NO
     |      |
   allow   401
```

Never assume that a controller method is protected merely because
the corresponding UI button is hidden.

Always test direct route access.

---

# 8. Ancestor / Feature Authorization

Some routes may not have an explicit ACL entry.

When the branch 2.2 implementation supports feature/ancestor fallback,
authorization must be evaluated according to the actual
`isAuthorizedByFeature()` behavior.

Conceptually:

```text
admin.leads.search
       |
       v
admin.leads
       |
       v
leads.*
```

An unmapped child route must not accidentally become publicly
accessible to an authenticated user.

When adding a new route:

1. Search for an exact ACL mapping.
2. Search for ancestor/feature authorization.
3. Determine whether an allow-list applies.
4. Confirm the final middleware behavior.
5. Add an authorization regression test.

---

# 9. Allow-list

Some routes may intentionally bypass normal ACL authorization.

Examples may include:

* account/profile functionality
* password-related functionality
* session-related endpoints
* saved filters
* TinyMCE upload
* web-form endpoints
* inbound mail processing

Do not add a route to an authorization allow-list simply to make
a failing test pass.

Before modifying the allow-list:

1. Understand what data the route exposes.
2. Determine whether authentication is sufficient.
3. Determine whether the route performs a privileged operation.
4. Check whether an existing ACL permission should protect it.
5. Add a security regression test.

Prefer explicit authorization over broad allow-list exceptions.

---

# 10. UI Permission Checks

ACL is also used to control UI visibility.

Common locations include:

```text
Menu
Blade views
Admin components
Action buttons
Mass actions
Settings tiles
```

Typical pattern:

```php
bouncer()->hasPermission('leads.delete')
```

This can determine whether the user sees:

* Delete buttons
* Create buttons
* Edit actions
* Mass actions
* Navigation entries
* Settings options

However:

```text
UI permission != security boundary
```

A hidden button does not prevent:

```text
POST /admin/...
```

The server-side authorization layer must still reject unauthorized
requests.

---

# 11. View Permission

View permission controls the data scope of a user.

Values:

```text
global
group
individual
```

Conceptual behavior:

```text
global
   |
   v
unrestricted data scope

group
   |
   v
records associated with users
in the caller's groups

individual
   |
   v
records belonging to the current user
```

The implementation should be traced through:

```text
Bouncer::getAuthorizedUserIds()
```

before changing behavior.

---

# 12. Authorized User IDs

The data-scope system should resolve to an authorized user-ID set.

Conceptually:

```text
global
   -> null
   -> no user_id restriction

group
   -> group member IDs

individual
   -> current user's ID
```

Important:

`null` must be interpreted according to the actual call site.

Do not automatically add:

```php
whereIn('user_id', null)
```

for global access.

Check how each repository/datagrid/controller handles the
authorized-ID result. The prevailing branch 2.2 idiom relies on
truthiness, so a `null` result adds no constraint at all:

```php
if ($userIds = bouncer()->getAuthorizedUserIds()) {
    $query->whereIn('leads.user_id', $userIds);
}
```

---

# 13. Data-Scoping Enforcement

Data scope can be enforced in several ways.

## Query filtering

Example:

```php
$query->whereIn('user_id', $authorizedUserIds);
```

Potential locations include datagrids and repositories.

## Single-record authorization

A controller may check ownership before returning a record.

Conceptually:

```text
requested record
      |
      v
record owner
      |
      v
authorized user IDs
      |
      +-- match -> allow
      |
      +-- no match -> 401
```

## Collection filtering

An already-loaded collection may be filtered to authorized records.

When modifying data scope, search for all three patterns.

---

# 14. Record Ownership

Data visibility commonly depends on an ownership field such as:

```text
user_id
```

When creating records, check whether the owner is assigned.

A non-global user may otherwise create or receive a record that
cannot later be retrieved through normal scoped queries.

When modifying record creation:

1. Identify the owner field.
2. Determine the default owner.
3. Determine behavior when owner is null.
4. Verify global users.
5. Verify group users.
6. Verify individual users.
7. Add regression tests.

Never assume all Krayin entities use the same ownership mechanism.

---

# 15. Two-Gate Authorization Example

Example user:

```text
Role:
    permission_type = custom

Permissions:
    leads
    leads.view
    leads.edit

View permission:
    group
```

Request:

```text
GET /admin/leads/view/42
```

Authorization flow:

```text
1. Authenticate user
2. Verify active status
3. Resolve role
4. Resolve route ACL
5. Check leads.view
6. Pass action authorization
7. Load lead 42
8. Resolve authorized user IDs
9. Check lead ownership/scope
10. Allow or deny
```

Therefore:

```text
ACL permission exists
        +
record belongs to authorized scope
        =
request allowed
```

Having the ACL permission alone does not automatically grant access
to every record.

---

# 16. Privilege Escalation Protection

Role administration is itself a privileged operation.

A non-administrator must not be able to create or assign privileges
greater than the privileges they possess.

Conceptual permission ceiling:

```text
Actor permissions
       |
       v
Target permissions
       |
       v
Target is a subset of Actor
```

If:

```text
Actor:
    leads.view
    leads.edit
```

The actor must not create a role containing:

```text
leads.view
leads.edit
leads.delete
```

because `leads.delete` is not in the actor's permissions.

This prevents privilege escalation through role administration.

---

# 17. Role Management Guards

When changing role-management functionality, inspect guards such as:

```text
Acl::getAuthorizedItems()
canManageRole()
self-role edit protection
preventUnauthorizedRoleAssignment()
```

Expected security principles:

* Non-admin users cannot create unrestricted administrator roles.
* Non-admin users cannot grant permissions they do not possess.
* Non-admin users cannot edit a role broader than their own authority.
* Users cannot bypass permission restrictions through role assignment.
* Self-role modification must be protected against privilege escalation.

Always verify actual branch 2.2 implementation before changing these
rules.

---

# 18. Data-Scope Escalation Protection

View permissions represent increasing authority.

Conceptually:

```text
individual < group < global
```

A user should not be able to assign a broader data scope than the
authorization rules allow.

When modifying scope assignment:

1. Identify actor scope.
2. Identify target scope.
3. Check target's existing scope.
4. Apply the branch 2.2 ceiling rules.
5. Test escalation attempts.
6. Test administrator behavior.
7. Test self-edit restrictions.

---

# 19. Self-Privilege Protection

A user modifying their own account must not be able to use a normal
account-edit operation to grant themselves greater authority.

Test:

```text
Own role
Own view_permission
Own status
```

Pay special attention to:

```text
role_id
view_permission
status
```

A privilege escalation test should attempt to modify the user's own
authorization state directly through the request layer.

---

# 20. User Status

User status participates in authorization.

Conceptually:

```text
active user
    |
    v
request continues

inactive user
    |
    v
session invalidation / logout
    |
    v
request denied
```

When modifying user-status authorization behavior, test:

1. User is active.
2. User becomes inactive.
3. Existing session makes another request.
4. New request is rejected.
5. Session/token state is handled correctly.

---

# 21. Security Testing Strategy

Every authorization change must be tested at multiple levels.

## Positive test

Verify an authorized user can perform the operation.

```text
permission = present
scope = authorized
result = success
```

## Negative ACL test

Remove the required permission.

```text
permission = absent
scope = authorized
result = 401/denied
```

## Negative scope test

Keep the ACL permission but use a record outside the user's scope.

```text
permission = present
scope = unauthorized
result = 401/denied
```

## Direct-request test

Do not rely only on UI.

Attempt the protected operation directly through:

* browser URL
* HTTP request
* API request where applicable
* POST/PUT/PATCH/DELETE request
* mass-action endpoint

Use the `pest-testing` skill for the test-authoring mechanics.

---

# 22. Required ACL Test Matrix

For a new permission, test:

| Scenario                                 | Expected      |
| ---------------------------------------- | ------------- |
| Administrator                            | Allowed       |
| Permission present                       | Allowed       |
| Permission absent                        | Denied        |
| UI permission absent                     | Action hidden |
| Direct route without permission          | Denied        |
| Direct POST without permission           | Denied        |
| Correct permission + unauthorized record | Denied        |
| Correct permission + authorized record   | Allowed       |

Where applicable also test:

```text
global
group
individual
```

---

# 23. Role Escalation Test Matrix

For role management:

| Scenario                             | Expected |
| ------------------------------------ | -------- |
| Admin creates role                   | Allowed  |
| Non-admin creates subset role        | Allowed  |
| Non-admin grants missing permission  | Denied   |
| Non-admin creates administrator role | Denied   |
| Non-admin edits broader role         | Denied   |
| User edits own role                  | Denied   |
| User assigns unauthorized role       | Denied   |

Adjust this matrix to the exact branch 2.2 implementation.

---

# 24. Scope Escalation Test Matrix

Test:

```text
individual -> individual
individual -> group
individual -> global

group -> individual
group -> group
group -> global

global -> individual
global -> group
global -> global
```

Expected behavior must follow the branch 2.2 scope-ceiling implementation.

Do not assume that all transitions are allowed or denied without
checking the actual controller logic.

---

# 25. Debugging Workflow

When an authorization bug is reported, follow this order:

```text
1. Identify user
2. Identify role
3. Identify permission_type
4. Inspect role permissions
5. Identify view_permission
6. Identify requested route
7. Resolve route -> ACL key
8. Inspect Bouncer middleware
9. Check UI permission
10. Check controller authorization
11. Check data-scope filtering
12. Check ownership
13. Reproduce authorized case
14. Reproduce unauthorized case
15. Add regression test
```

Never fix an authorization bug by changing only the UI.

---

# 26. Adding a New ACL Permission

When implementing a new feature:

## Step 1

Find similar existing permissions.

## Step 2

Add the permission to the appropriate ACL configuration.

## Step 3

Map all required routes.

For example:

```text
index
create
store
edit
update
delete
mass actions
```

Do not assume one route automatically protects all related actions.

## Step 4

Check menu permissions.

## Step 5

Check Blade/component permissions.

## Step 6

Verify middleware authorization.

## Step 7

Verify controller/data authorization.

## Step 8

Add automated tests.

---

# 27. Adding a New Role

Before creating a role:

```text
Inspect actor permissions
        |
        v
Build requested permission set
        |
        v
Check requested is a subset of actor
        |
        v
Check role type
        |
        v
Check existing role restrictions
        |
        v
Create/update role
```

For non-administrators, never silently broaden privileges.

---

# 28. Code Review Rules

When reviewing a Krayin authorization PR:

Check:

* Does every privileged route have authorization?
* Is the ACL permission mapped correctly?
* Does the permission naming follow existing conventions?
* Is the UI check present where appropriate?
* Is server-side authorization enforced?
* Is record-level scope enforced?
* Can a user bypass authorization through a direct request?
* Can a user escalate through role management?
* Can a user escalate through view permission?
* Are existing roles backward compatible?
* Are regression tests included?
* Are administrator paths still correct?
* Are allow-list changes justified?

Treat missing server-side authorization as a security issue.

---

# 29. Important Distinction

Always distinguish these three concepts:

```text
ACL
    |
    v
Can the user perform this action?

View Permission
    |
    v
Which records can the user access?

UI Permission Check
    |
    v
Should the interface show this action?
```

They are related but not interchangeable.

Correct:

```text
ACL -> action authorization
Scope -> record authorization
UI -> presentation
```

Incorrect:

```text
Button hidden -> operation secure
```

---

# 30. Security Principles

Follow these principles for every authorization change:

1. Fail closed.
2. Enforce authorization server-side.
3. Never rely on hidden UI controls.
4. Do not grant permissions implicitly.
5. Prevent privilege escalation.
6. Treat role management as a privileged operation.
7. Treat data scope separately from action permissions.
8. Validate direct requests.
9. Add regression tests for authorization bugs.
10. Follow existing Krayin branch 2.2 patterns.
11. Inspect actual implementation before making architectural assumptions.
12. Prefer the smallest safe change.

---

# 31. Expected Agent Behavior

When asked to modify Krayin authorization:

1. Inspect the existing implementation.
2. Identify the authorization layer involved.
3. Determine whether the issue concerns:

   * ACL
   * route mapping
   * UI permission
   * data scope
   * ownership
   * role escalation
   * scope escalation
4. Find a comparable existing implementation.
5. Explain the affected authorization flow.
6. Make the smallest appropriate change.
7. Add/update regression tests.
8. Test both positive and negative authorization cases.
9. Check for privilege escalation.
10. Report any assumptions explicitly.

Never invent Krayin classes, routes, permission keys, or database
behavior.

Always verify them against the branch 2.2 source.

---

# 32. Definition of Done

An authorization feature is complete only when:

* [ ] ACL permission is defined correctly.
* [ ] Required routes are mapped.
* [ ] Middleware authorization works.
* [ ] UI permissions are correct.
* [ ] Data scope is enforced.
* [ ] Record ownership is validated.
* [ ] Administrator behavior is verified.
* [ ] Unauthorized direct requests are rejected.
* [ ] Role escalation is prevented.
* [ ] Scope escalation is prevented where applicable.
* [ ] Regression tests exist.
* [ ] Existing functionality is not unintentionally restricted.
* [ ] Code follows Krayin branch 2.2 conventions.

---

# 33. Canonical Authorization Flow

Use this flow when explaining Krayin 2.2 authorization:

```text
USER
 |
 +-- role_id ---------------> ROLES
 |                              |
 |                              +-- permission_type
 |                              +-- permissions
 |
 +-- view_permission
          |
          v
    DATA SCOPE
          |
          +-- global
          +-- group ------> user_groups ------> groups
          +-- individual


ADMIN REQUEST
      |
      v
Authentication
      |
      v
User status
      |
      v
Role
      |
      v
Administrator?
      |
      +-- YES ---------------> ACL action gate passed
      |
      +-- NO
           |
           v
      Route -> ACL key
           |
           v
      User has permission?
           |
        +--+--+
       YES    NO
        |      |
        |     401
        v
   Controller
        |
        v
Record/data scope
        |
        v
Authorized owner?
        |
     +--+--+
    YES    NO
     |      |
   ALLOW   401
```

The key architectural principle is:

```text
ACTION AUTHORIZATION
        +
DATA AUTHORIZATION
        =
EFFECTIVE ACCESS
```

Do not collapse these into a single ACL concept.
