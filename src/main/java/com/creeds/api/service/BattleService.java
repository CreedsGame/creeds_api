package com.creeds.api.service;

import com.creeds.api.model.Battle;
import com.creeds.api.model.Character;
import com.creeds.api.model.Turn;
import com.creeds.api.repository.BattleRepository;
import com.creeds.api.repository.CharacterRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;

@Service
@RequiredArgsConstructor
public class BattleService {
    
    private final BattleRepository battleRepository;
    private final CharacterRepository characterRepository;
    private final Random random = new Random();
    
    @Transactional
    public Battle executeBattle(String fighterName, String opponentName) {
        Character fighter = characterRepository.findByName(fighterName)
                .orElseThrow(() -> new RuntimeException("Fighter not found"));
        Character opponent = characterRepository.findByName(opponentName)
                .orElseThrow(() -> new RuntimeException("Opponent not found"));
        
        Battle battle = new Battle();
        battle.setBattleId(generateBattleId(fighterName));
        battle.setFighter(fighter);
        battle.setOpponent(opponent);
        
        // Determine starting player based on initiative
        String startingPlayer = determineStartingPlayer(fighter, opponent);
        battle.setStartingPlayer(startingPlayer);
        
        // Initialize life points
        int fighterLife = fighter.getLife();
        int opponentLife = opponent.getLife();
        battle.setFighterLife(fighterLife);
        battle.setOpponentLife(opponentLife);
        
        // Execute battle turns
        List<Turn> turns = executeBattleTurns(fighter, opponent, startingPlayer);
        battle.setTurns(turns);
        
        // Determine winner
        String winner = turns.get(turns.size() - 1).getExecutor();
        battle.setWinner(winner);
        battle.setLoser(winner.equals(fighterName) ? opponentName : fighterName);
        
        return battleRepository.save(battle);
    }
    
    private String generateBattleId(String characterName) {
        return characterName + ":" + System.currentTimeMillis();
    }
    
    private String determineStartingPlayer(Character fighter, Character opponent) {
        if (fighter.getInitiative() > opponent.getInitiative()) {
            return fighter.getName();
        } else if (opponent.getInitiative() > fighter.getInitiative()) {
            return opponent.getName();
        } else {
            return random.nextBoolean() ? fighter.getName() : opponent.getName();
        }
    }
    
    private List<Turn> executeBattleTurns(Character fighter, Character opponent, String startingPlayer) {
        List<Turn> turns = new ArrayList<>();
        int fighterLife = fighter.getLife();
        int opponentLife = opponent.getLife();
        String currentPlayer = startingPlayer;
        
        while (fighterLife > 0 && opponentLife > 0) {
            Turn turn = new Turn();
            turn.setTurnNumber(turns.size() + 1);
            turn.setExecutor(currentPlayer);
            turn.setReceiver(currentPlayer.equals(fighter.getName()) ? opponent.getName() : fighter.getName());
            
            // Calculate damage
            Character attacker = currentPlayer.equals(fighter.getName()) ? fighter : opponent;
            Character defender = currentPlayer.equals(fighter.getName()) ? opponent : fighter;
            int damage = calculateDamage(attacker, defender);
            
            // Critical hit chance (1/10)
            boolean isCritical = random.nextInt(10) == 0;
            if (isCritical) {
                damage *= 2;
                turn.setAction("CRITICAL HIT");
            } else {
                turn.setAction("HIT");
            }
            
            turn.setDamage(damage);
            
            // Apply damage
            if (currentPlayer.equals(fighter.getName())) {
                opponentLife -= damage;
            } else {
                fighterLife -= damage;
            }
            
            turns.add(turn);
            currentPlayer = currentPlayer.equals(fighter.getName()) ? opponent.getName() : fighter.getName();
        }
        
        return turns;
    }
    
    private int calculateDamage(Character attacker, Character defender) {
        double str = attacker.getStrength();
        double end = defender.getEndurance();
        double atk = 1.0; // Base attack value
        double mod = 1.0; // Base modifier
        double rnd = 0.8 + (random.nextDouble() * 0.4); // Random factor between 0.8 and 1.2
        
        return (int) Math.floor(5 * Math.sqrt(str/end * atk) * mod * rnd);
    }
} 