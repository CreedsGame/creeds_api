# Creeds API Flow Diagrams

This document contains detailed flow diagrams for the Creeds Game API.

## Character Creation Flow

```mermaid
sequenceDiagram
    participant Client
    participant Controller
    participant Service
    participant Repository
    participant Database

    Client->>Controller: POST /characters (name, password)
    Controller->>Service: createCharacter(name, password)
    Service->>Repository: existsByName(name)
    Repository->>Database: SELECT COUNT(*) WHERE name = ?
    Database-->>Repository: count result
    Repository-->>Service: boolean exists
    
    alt Character name exists
        Service-->>Controller: Throw RuntimeException
        Controller-->>Client: 500 Error
    else Character name available
        Service->>Service: Create Character entity
        Service->>Service: Calculate life points (100 + level * 10)
        Service->>Repository: save(character)
        Repository->>Database: INSERT INTO characters
        Database-->>Repository: Saved character
        Repository-->>Service: Character entity
        Service-->>Controller: Character object
        Controller-->>Client: 200 OK + Character JSON
    end
```

## Battle Execution Flow

```mermaid
sequenceDiagram
    participant Client
    participant BattleController
    participant BattleService
    participant CharacterRepository
    participant BattleRepository
    participant Database

    Client->>BattleController: POST /battles (fighter, opponent)
    BattleController->>BattleService: executeBattle(fighter, opponent)
    
    BattleService->>CharacterRepository: findByName(fighter)
    CharacterRepository->>Database: SELECT * FROM characters WHERE name = ?
    Database-->>CharacterRepository: Fighter character
    CharacterRepository-->>BattleService: Fighter entity
    
    BattleService->>CharacterRepository: findByName(opponent)
    CharacterRepository->>Database: SELECT * FROM characters WHERE name = ?
    Database-->>CharacterRepository: Opponent character
    CharacterRepository-->>BattleService: Opponent entity
    
    BattleService->>BattleService: Determine starting player (initiative)
    BattleService->>BattleService: Initialize battle entity
    BattleService->>BattleService: Execute battle turns loop
    
    loop Battle Turns
        BattleService->>BattleService: Calculate damage (str/end formula)
        BattleService->>BattleService: Check critical hit (10% chance)
        BattleService->>BattleService: Apply damage to life points
        BattleService->>BattleService: Create Turn entity
        BattleService->>BattleService: Switch players
    end
    
    BattleService->>BattleService: Determine winner/loser
    BattleService->>BattleRepository: save(battle)
    BattleRepository->>Database: INSERT INTO battles + turns
    Database-->>BattleRepository: Saved battle
    BattleRepository-->>BattleService: Battle entity
    BattleService-->>BattleController: Battle object
    BattleController-->>Client: 200 OK + Battle JSON
```

## Battle Turn Logic Flow

```mermaid
flowchart TD
    A[Start Battle] --> B[Determine Starting Player]
    B --> C{Compare Initiative}
    C -->|Fighter > Opponent| D[Fighter Starts]
    C -->|Opponent > Fighter| E[Opponent Starts]
    C -->|Equal| F[Random Choice]
    F --> D
    F --> E
    
    D --> G[Initialize Life Points]
    E --> G
    
    G --> H[Begin Turn Loop]
    H --> I{Both Characters Alive?}
    I -->|No| Z[End Battle - Determine Winner]
    I -->|Yes| J[Get Current Attacker]
    
    J --> K[Calculate Base Damage]
    K --> L[Formula: 5 * sqrt str/end * atk * mod * rnd]
    L --> M{Critical Hit? 10% chance}
    M -->|Yes| N[Double Damage]
    M -->|No| O[Normal Damage]
    
    N --> P[Apply Damage to Defender]
    O --> P
    
    P --> Q[Create Turn Record]
    Q --> R[Switch Players]
    R --> I
    
    Z --> S[Save Battle to Database]
    S --> T[Return Battle Result]
```

## Character Login Flow

```mermaid
sequenceDiagram
    participant Client
    participant Controller
    participant Service
    participant Repository
    participant Database

    Client->>Controller: POST /characters/login (name, password)
    Controller->>Service: validateLogin(name, password)
    Service->>Repository: findByName(name)
    Repository->>Database: SELECT * FROM characters WHERE name = ?
    
    alt Character not found
        Database-->>Repository: No results
        Repository-->>Service: Optional.empty()
        Service-->>Controller: false
        Controller-->>Client: 200 OK + false
    else Character found
        Database-->>Repository: Character entity
        Repository-->>Service: Optional<Character>
        Service->>Service: Compare passwords
        alt Password matches
            Service-->>Controller: true
            Controller-->>Client: 200 OK + true
        else Password doesn't match
            Service-->>Controller: false
            Controller-->>Client: 200 OK + false
        end
    end
```

## System Architecture

```mermaid
graph TB
    subgraph "Client Layer"
        A[Web Browser]
        B[Mobile App]
        C[Postman/cURL]
    end
    
    subgraph "API Layer"
        D[CharacterController]
        E[BattleController]
    end
    
    subgraph "Service Layer"
        F[CharacterService]
        G[BattleService]
    end
    
    subgraph "Repository Layer"
        H[CharacterRepository]
        I[BattleRepository]
    end
    
    subgraph "Database Layer"
        J[(H2 Database)]
    end
    
    A --> D
    A --> E
    B --> D
    B --> E
    C --> D
    C --> E
    
    D --> F
    E --> G
    
    F --> H
    G --> H
    G --> I
    
    H --> J
    I --> J
```

## Data Flow - Battle Execution

```mermaid
flowchart LR
    A[Client Request] --> B[BattleController]
    B --> C[BattleService]
    C --> D[Load Fighter]
    C --> E[Load Opponent]
    D --> F[CharacterRepository]
    E --> F
    F --> G[(Database)]
    G --> F
    F --> C
    C --> H[Calculate Turns]
    H --> I[Save Battle]
    I --> J[BattleRepository]
    J --> G
    G --> J
    J --> I
    I --> C
    C --> B
    B --> K[Client Response]
```

## Entity Relationships

```mermaid
erDiagram
    CHARACTER ||--o{ BATTLE : "fighter"
    CHARACTER ||--o{ BATTLE : "opponent"
    BATTLE ||--o{ TURN : "contains"
    
    CHARACTER {
        bigint id PK
        string name UK
        string password
        int level
        int strength
        int endurance
        int initiative
        int life
    }
    
    BATTLE {
        string battle_id PK
        bigint fighter_id FK
        bigint opponent_id FK
        string starting_player
        string winner
        string loser
        int fighter_life
        int opponent_life
        timestamp creation_date
    }
    
    TURN {
        bigint id PK
        string battle_id FK
        int turn_number
        string executor
        string receiver
        string action
        int damage
    }
```

## How to Render These Diagrams

### Online Tools
- [Mermaid Live Editor](https://mermaid.live/)
- [GitHub](https://github.com) - Automatically renders Mermaid diagrams in markdown files

### VS Code Extension
- Install "Markdown Preview Mermaid Support" extension
- Open this file and use the preview pane

### Documentation Tools
- [GitBook](https://www.gitbook.com/) - Supports Mermaid
- [Confluence](https://www.atlassian.com/software/confluence) - With Mermaid plugin
- [Notion](https://www.notion.so/) - Supports Mermaid blocks

