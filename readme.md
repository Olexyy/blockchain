____Notes about blockchain implementation.____

- Each blockchain node has: 
  - unique uuid among other nodes;
  - open api to expose its blockchain;
  - announces any changes in its blockchain  to known nodes;
  - on announce queries announcer for changes;
  - solves collisions on update;
  
- Collisions convention:
  - longer valid chain wins;
  - own blocks in invalid chain should be re-added and re-announced;
  
- Steps for blocks generation:
  - obtain data and create block;
  - calculate hash -> obtain nonce;
  - fill block and insert into own blockchain;
  - announce changes for known nodes;
  
- Steps for reacting on announcer:
  - read initial message (has count, hash and nonce);
  - react only on equal or more count;
  - act according to collision convention;
  - announce changes if any;
  
- Settings:
  - blockchain id;
  - blockchain node id;
  - single/multiple;
  - block pool managing:
    - immediate (batch);
    - CRON (queue);
  - announce managing:
    - queue (CRON);
    - immediate;
  - interval CRON announce;
  - interval CRON block pool;
  - pow_position;
  - pow_expression;
  - use auth;
  - whitelist/blacklist filtering;
  
  TODOS:
  - blockchain API service (protocol);
    - subscribe: OK
    - announce: ~
    - count: ~ add to tests
    - sync ->
    - fetch ->
  - blockchain API validate OK;
  - blockchain node management (service) OK;
  - list and admin management for blockchain OK;
  - business data as plugins (selected in settings) dynamic ~;
  - field type, formatter and widget OK;
  - settings:
    - blacklist/whitelist OK;
    - use auth OK;
  - blockchain response class OK
  - possibility to assign blockchain id and node id if bc is empty ~
  - HTTP/S + config ~
  - announce all with promise https://github.com/guzzle/guzzle/issues/1481 ~
  - optimize announce all
  - ! locker service
  - ! sync action
  - ! fetch action
  - ! cache service/extension
  - ! handle conflicts