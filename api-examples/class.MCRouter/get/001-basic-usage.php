<?hh

namespace Hack\UserDocumentation\API\Examples\MCRouter\MCrouter\Get;

function get_simple_mcrouter(): \MCRouter {
  $servers = Vector { \getenv('HHVM_TEST_MCROUTER') };
  $mc = \MCRouter::createSimple($servers);
  return $mc;
}

async function set_value(\MCRouter $mc, string $key,
                         string $value): Awaitable<void> {
  // can also pass optional int flags and int expiration time (in seconds)
  await $mc->set($key, $value);
}

async function get_value(\MCRouter $mc, string $key): Awaitable<?string> {
  $ret = null;
  try {
    $ret = await $mc->get($key);
  } catch (\MCRouterException $ex) { // e.g., exception if key doesn't exist
    \var_dump($ex->getMessage());
  }
  return $ret;
}

async function run(): Awaitable<void> {
  $mc = get_simple_mcrouter();
  $unique_key = \str_shuffle('ABCDEFGHIJKLMN');
  await set_value($mc, $unique_key, "Hi");
  $val = await get_value($mc, $unique_key);
  \var_dump($val);
  // Getting a non-existent key is no good
  $val = await get_value($mc, "THISKEYDOESNOTEXISTIHOPE");
  if (!$val) {
    echo "Key must not have existed\n";
  }
}

\HH\Asio\join(run());
