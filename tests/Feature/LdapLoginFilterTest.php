<?php

use App\Http\Controllers\Auth\LoginController;
use LdapRecord\Query\Builder as LdapQueryBuilder;

/**
 * Regression test for the login crash caused by passing a Closure to
 * LdapRecord\Query\Builder::where(), which only accepts array|string
 * (unlike Eloquent's where()). See LoginController::buildLdapUserQuery().
 */
function buildLdapUserQueryForTest(array $attrs, ?string $group = null, ?string $baseDn = null): LdapQueryBuilder
{
    config([
        'app.ldap_group' => $group,
        'app.ldap_users_base_dn' => $baseDn,
    ]);

    $controller = new LoginController();

    $method = new ReflectionMethod(LoginController::class, 'buildLdapUserQuery');
    $method->setAccessible(true);

    return $method->invoke($controller, 'jdoe', $attrs);
}

it('builds an OR filter across multiple LDAP login attributes without throwing', function () {
    $query = buildLdapUserQueryForTest(['uid', 'sAMAccountName', 'mail']);

    // LdapRecord wraps the nested orFilter() group in its own '(|...)',
    // so a lone OR group is doubly-wrapped; this is redundant but valid LDAP.
    expect($query->getUnescapedQuery())
        ->toBe('(|(|(uid=jdoe)(sAMAccountName=jdoe)(mail=jdoe)))');
});

it('keeps the memberOf group restriction outside of the attributes OR group', function () {
    $query = buildLdapUserQueryForTest(['uid', 'mail'], group: 'cn=admins,dc=example,dc=com');

    $filter = $query->getUnescapedQuery();

    expect($filter)
        ->toContain('(memberOf=cn=admins,dc=example,dc=com)')
        ->toContain('(|(uid=jdoe)(mail=jdoe))')
        ->not->toContain('(memberOf=cn=admins,dc=example,dc=com)(uid=jdoe)');
});
