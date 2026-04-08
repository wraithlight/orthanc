type EqupimentType = "NORMAL" | "EPIC";

type SwordEqupimentType = EqupimentType;
type ShieldEqupimentType = EqupimentType;
type ArmorEqupimentType = EqupimentType;
type BowEqupimentType = EqupimentType;

export interface GameEquipment {
  sword: SwordEqupimentType;
  shield: ShieldEqupimentType;
  armor: ArmorEqupimentType;
  bow: BowEqupimentType;
  arrows: number;
}
