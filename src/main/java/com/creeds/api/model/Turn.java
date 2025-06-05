package com.creeds.api.model;

import jakarta.persistence.*;
import lombok.Data;
import lombok.NoArgsConstructor;

@Data
@NoArgsConstructor
@Entity
@Table(name = "turns")
public class Turn {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "battle_id", nullable = false)
    private Battle battle;
    
    @Column(nullable = false)
    private Integer turnNumber;
    
    @Column(nullable = false)
    private String action;
    
    @Column(nullable = false)
    private Integer damage;
    
    @Column(nullable = false)
    private String executor;
    
    @Column(nullable = false)
    private String receiver;
    
    public void setBattle(Battle battle) {
        this.battle = battle;
        if (battle != null && !battle.getTurns().contains(this)) {
            battle.getTurns().add(this);
        }
    }
} 