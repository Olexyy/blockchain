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
  - business data as plugins (selected in settings) dynamic ~;
  - announce all with promise https://github.com/guzzle/guzzle/issues/1481 ~
  - optimize announce all
  - ! locker service
  - ! sync action
  - ! fetch action
  - ! cache service/extension !!!SharedTempStore!!!
  - ! handle conflicts
  - setting for pull block limit ->