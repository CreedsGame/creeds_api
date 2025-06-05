package com.creeds.api.model;

import jakarta.persistence.*;
import lombok.Data;
import lombok.NoArgsConstructor;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;

@Data
@NoArgsConstructor
@Entity
@Table(name = "battles")
public class Battle {
    
    @Id
    @Column(nullable = false)
    private String battleId;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "fighter_id", nullable = false)
    private Character fighter;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "opponent_id", nullable = false)
    private Character opponent;
    
    @Column(nullable = false)
    private String startingPlayer;
    
    @Column(nullable = false)
    private String winner;
    
    @Column(nullable = false)
    private String loser;
    
    @Column(nullable = false)
    private Integer fighterLife;
    
    @Column(nullable = false)
    private Integer opponentLife;
    
    @Column(nullable = false)
    private LocalDateTime creationDate = LocalDateTime.now();
    
    @OneToMany(mappedBy = "battle", cascade = CascadeType.ALL, orphanRemoval = true)
    private List<Turn> turns = new ArrayList<>();
    
    public void addTurn(Turn turn) {
        turns.add(turn);
        turn.setBattle(this);
    }
    
    public void setTurns(List<Turn> turns) {
        this.turns.clear();
        if (turns != null) {
            turns.forEach(this::addTurn);
        }
    }
} 